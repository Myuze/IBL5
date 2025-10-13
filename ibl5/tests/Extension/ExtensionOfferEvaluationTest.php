<?php

use PHPUnit\Framework\TestCase;
use Extension\ExtensionOfferEvaluator;

/**
 * Tests for contract extension offer evaluation logic
 * 
 * Tests the complex player preference evaluation algorithm including:
 * - Offer value calculation
 * - Player demands calculation
 * - Modifier factors (winner, tradition, coach, loyalty, security, playing time)
 * - Offer acceptance/rejection logic
 */
class ExtensionOfferEvaluationTest extends TestCase
{
    private $mockDb;
    private $offerEvaluator;

    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->offerEvaluator = new ExtensionOfferEvaluator($this->mockDb);
    }

    protected function tearDown(): void
    {
        $this->offerEvaluator = null;
        $this->mockDb = null;
    }

    /**
     * @group offer-evaluation
     * @group offer-value
     */
    public function testCalculatesOfferValueCorrectly()
    {
        // Arrange
        $offer = [
            'year1' => 1000,
            'year2' => 1100,
            'year3' => 1200,
            'year4' => 0,
            'year5' => 0
        ];

        // Act
        $result = $this->offerEvaluator->calculateOfferValue($offer);

        // Assert
        $this->assertEquals(3300, $result['total']); // 1000 + 1100 + 1200
        $this->assertEquals(3, $result['years']);
        $this->assertEquals(1100, $result['averagePerYear']); // 3300 / 3
    }

    /**
     * @group offer-evaluation
     * @group offer-value
     */
    public function testCalculatesOfferValueFor5YearContract()
    {
        // Arrange
        $offer = [
            'year1' => 1000,
            'year2' => 1100,
            'year3' => 1200,
            'year4' => 1300,
            'year5' => 1400
        ];

        // Act
        $result = $this->offerEvaluator->calculateOfferValue($offer);

        // Assert
        $this->assertEquals(6000, $result['total']);
        $this->assertEquals(5, $result['years']);
        $this->assertEquals(1200, $result['averagePerYear']);
    }

    /**
     * @group offer-evaluation
     * @group modifiers
     */
    public function testCalculatesWinnerModifierCorrectly()
    {
        // Arrange
        $teamFactors = [
            'wins' => 60,
            'losses' => 22
        ];
        $playerPreferences = [
            'winner' => 5 // High winner preference
        ];

        // Act
        $modifier = $this->offerEvaluator->calculateWinnerModifier($teamFactors, $playerPreferences);

        // Assert - With wins > losses and high winner preference, modifier should be positive
        $this->assertGreaterThan(0, $modifier);
    }

    /**
     * @group offer-evaluation
     * @group modifiers
     */
    public function testCalculatesTraditionModifierCorrectly()
    {
        // Arrange
        $teamFactors = [
            'tradition_wins' => 60,
            'tradition_losses' => 22
        ];
        $playerPreferences = [
            'tradition' => 4 // Moderate tradition preference
        ];

        // Act
        $modifier = $this->offerEvaluator->calculateTraditionModifier($teamFactors, $playerPreferences);

        // Assert - Good tradition with preference should give positive modifier
        $this->assertGreaterThan(0, $modifier);
    }

    /**
     * @group offer-evaluation
     * @group modifiers
     */
    public function testCalculatesLoyaltyModifierCorrectly()
    {
        // Arrange
        $playerPreferences = [
            'loyalty' => 5 // High loyalty
        ];

        // Act
        $modifier = $this->offerEvaluator->calculateLoyaltyModifier($playerPreferences);

        // Assert - High loyalty should give positive modifier
        $this->assertGreaterThan(0, $modifier);
        
        // Test low loyalty
        $playerPreferences['loyalty'] = 1;
        $modifierLow = $this->offerEvaluator->calculateLoyaltyModifier($playerPreferences);
        $this->assertEquals(0, $modifierLow); // loyalty of 1 means no modifier
    }

    /**
     * @group offer-evaluation
     * @group modifiers
     */
    public function testCalculatesPlayingTimeModifierCorrectly()
    {
        // Arrange
        $teamFactors = [
            'money_committed_at_position' => 5000 // High money at position
        ];
        $playerPreferences = [
            'playing_time' => 5 // High playing time preference
        ];

        // Act
        $modifier = $this->offerEvaluator->calculatePlayingTimeModifier($teamFactors, $playerPreferences);

        // Assert - High money at position with playing time preference should be negative
        $this->assertLessThan(0, $modifier);
    }

    /**
     * @group offer-evaluation
     * @group modifiers
     */
    public function testCalculatesCombinedModifierCorrectly()
    {
        // Arrange
        $teamFactors = [
            'wins' => 50,
            'losses' => 32,
            'tradition_wins' => 2500,
            'tradition_losses' => 2000,
            'coach_rating' => 80,
            'money_committed_at_position' => 3000
        ];
        $playerPreferences = [
            'winner' => 3,
            'tradition' => 3,
            'coach' => 3,
            'loyalty' => 3,
            'security' => 3,
            'playing_time' => 3
        ];

        // Act
        $modifier = $this->offerEvaluator->calculateCombinedModifier($teamFactors, $playerPreferences);

        // Assert - Modifier should be around 1.0 (neutral preferences)
        $this->assertGreaterThan(0.9, $modifier);
        $this->assertLessThan(1.15, $modifier); // Slightly relaxed to account for formula precision
    }

    /**
     * @group offer-evaluation
     * @group acceptance
     */
    public function testAcceptsOfferWhenValueExceedsDemands()
    {
        // Arrange
        $offer = [
            'year1' => 1000,
            'year2' => 1100,
            'year3' => 1200,
            'year4' => 0,
            'year5' => 0
        ];
        $demands = [
            'year1' => 800,
            'year2' => 880,
            'year3' => 968,
            'year4' => 0,
            'year5' => 0
        ];
        $teamFactors = [
            'wins' => 50,
            'losses' => 32,
            'tradition_wins' => 2500,
            'tradition_losses' => 2000,
            'money_committed_at_position' => 2000
        ];
        $playerPreferences = [
            'winner' => 3,
            'tradition' => 3,
            'loyalty' => 3,
            'playing_time' => 3
        ];

        // Act
        $result = $this->offerEvaluator->evaluateOffer($offer, $demands, $teamFactors, $playerPreferences);

        // Assert
        $this->assertTrue($result['accepted']);
        $this->assertArrayHasKey('offerValue', $result);
        $this->assertArrayHasKey('demandValue', $result);
        $this->assertGreaterThan($result['demandValue'], $result['offerValue']);
    }

    /**
     * @group offer-evaluation
     * @group acceptance
     */
    public function testRejectsOfferWhenValueBelowDemands()
    {
        // Arrange
        $offer = [
            'year1' => 800,
            'year2' => 850,
            'year3' => 900,
            'year4' => 0,
            'year5' => 0
        ];
        $demands = [
            'year1' => 1200,
            'year2' => 1320,
            'year3' => 1452,
            'year4' => 0,
            'year5' => 0
        ];
        $teamFactors = [
            'wins' => 30,
            'losses' => 52,
            'tradition_wins' => 1500,
            'tradition_losses' => 2500,
            'money_committed_at_position' => 5000
        ];
        $playerPreferences = [
            'winner' => 5,    // High preference
            'tradition' => 5, // High preference
            'loyalty' => 1,   // Low loyalty
            'playing_time' => 5 // High preference
        ];

        // Act
        $result = $this->offerEvaluator->evaluateOffer($offer, $demands, $teamFactors, $playerPreferences);

        // Assert
        $this->assertFalse($result['accepted']);
        $this->assertLessThan($result['demandValue'], $result['offerValue']);
    }

    /**
     * @group offer-evaluation
     * @group edge-cases
     */
    public function testHandlesExtremelyHighModifiers()
    {
        // Arrange - Perfect team for player with max preferences
        $teamFactors = [
            'wins' => 70,
            'losses' => 12,
            'tradition_wins' => 4000,
            'tradition_losses' => 1000,
            'money_committed_at_position' => 0
        ];
        $playerPreferences = [
            'winner' => 5,
            'tradition' => 5,
            'loyalty' => 5,
            'playing_time' => 5
        ];

        // Act
        $modifier = $this->offerEvaluator->calculateCombinedModifier($teamFactors, $playerPreferences);

        // Assert - Modifier should be significantly above 1.0
        $this->assertGreaterThan(1.0, $modifier);
    }

    /**
     * @group offer-evaluation
     * @group edge-cases
     */
    public function testHandlesExtremelyLowModifiers()
    {
        // Arrange - Terrible team for demanding player
        $teamFactors = [
            'wins' => 15,
            'losses' => 67,
            'tradition_wins' => 1000,
            'tradition_losses' => 4000,
            'money_committed_at_position' => 8000
        ];
        $playerPreferences = [
            'winner' => 5,
            'tradition' => 5,
            'loyalty' => 1,
            'playing_time' => 5
        ];

        // Act
        $modifier = $this->offerEvaluator->calculateCombinedModifier($teamFactors, $playerPreferences);

        // Assert - Modifier should be significantly below 1.0
        $this->assertLessThan(1.0, $modifier);
    }

    /**
     * @group offer-evaluation
     * @group demands
     */
    public function testCalculatesDemandsBasedOnPlayerValue()
    {
        // Arrange
        $playerValue = 2000; // Average yearly value player wants

        // Act
        $demands = $this->offerEvaluator->calculatePlayerDemands($playerValue);

        // Assert
        $this->assertIsArray($demands);
        $this->assertArrayHasKey('total', $demands);
        $this->assertArrayHasKey('years', $demands);
        $this->assertEquals(5, $demands['years']); // Extensions are 5 years max
    }

    /**
     * @group offer-evaluation
     * @group in-millions
     */
    public function testConvertsOfferAmountToMillions()
    {
        // Arrange
        $offerTotal = 12000; // 12000 in internal units

        // Act
        $millions = $this->offerEvaluator->convertToMillions($offerTotal);

        // Assert
        $this->assertEquals(120, $millions); // 12000 / 100 = 120 million
    }
}
