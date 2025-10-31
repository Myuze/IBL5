<?php

use PHPUnit\Framework\TestCase;
use Waivers\WaiversRepository;

class WaiversRepositoryTest extends TestCase
{
    private $mockDb;
    private $repository;
    
    protected function setUp(): void
    {
        $this->mockDb = new MockDatabase();
        $this->repository = new WaiversRepository($this->mockDb);
    }
    
    public function testGetUserByUsernameReturnsUserData()
    {
        $this->mockDb->setMockData([
            [
                'username' => 'testuser',
                'user_ibl_team' => 'Boston Celtics',
                'user_id' => 1
            ]
        ]);
        $this->mockDb->setNumRows(1);
        
        $user = $this->repository->getUserByUsername('testuser');
        
        $this->assertIsArray($user);
        $this->assertEquals('testuser', $user['username']);
        $this->assertEquals('Boston Celtics', $user['user_ibl_team']);
    }
    
    public function testGetUserByUsernameReturnsNullWhenNotFound()
    {
        $this->mockDb->setNumRows(0);
        
        $user = $this->repository->getUserByUsername('nonexistent');
        
        $this->assertNull($user);
    }
    
    public function testGetTeamByNameReturnsTeamData()
    {
        $this->mockDb->setMockData([
            [
                'team_name' => 'Boston Celtics',
                'teamid' => 2,
                'ownerName' => 'Test Owner'
            ]
        ]);
        $this->mockDb->setNumRows(1);
        
        $team = $this->repository->getTeamByName('Boston Celtics');
        
        $this->assertIsArray($team);
        $this->assertEquals('Boston Celtics', $team['team_name']);
        $this->assertEquals(2, $team['teamid']);
    }
    
    public function testGetTeamByNameReturnsNullWhenNotFound()
    {
        $this->mockDb->setNumRows(0);
        
        $team = $this->repository->getTeamByName('Nonexistent Team');
        
        $this->assertNull($team);
    }
    
    public function testGetTeamTotalSalaryCalculatesCorrectly()
    {
        $this->mockDb->setMockData([
            ['cy' => 1, 'cy1' => 500],
            ['cy' => 2, 'cy2' => 600],
            ['cy' => 1, 'cy1' => 300]
        ]);
        $this->mockDb->setNumRows(3);
        
        $totalSalary = $this->repository->getTeamTotalSalary('Boston Celtics');
        
        $this->assertEquals(1400, $totalSalary);
    }
    
    public function testGetTeamTotalSalaryReturnsZeroForEmptyTeam()
    {
        $this->mockDb->setNumRows(0);
        
        $totalSalary = $this->repository->getTeamTotalSalary('Empty Team');
        
        $this->assertEquals(0, $totalSalary);
    }
    
    public function testGetPlayerByIDReturnsPlayerData()
    {
        $this->mockDb->setMockData([
            [
                'pid' => 123,
                'name' => 'John Doe',
                'cy1' => 500,
                'exp' => 5
            ]
        ]);
        $this->mockDb->setNumRows(1);
        
        $player = $this->repository->getPlayerByID(123);
        
        $this->assertIsArray($player);
        $this->assertEquals(123, $player['pid']);
        $this->assertEquals('John Doe', $player['name']);
        $this->assertEquals(500, $player['cy1']);
    }
    
    public function testGetPlayerByIDReturnsNullWhenNotFound()
    {
        $this->mockDb->setNumRows(0);
        
        $player = $this->repository->getPlayerByID(999);
        
        $this->assertNull($player);
    }
    
    public function testDropPlayerToWaiversExecutesCorrectQuery()
    {
        $this->mockDb->setReturnTrue(true);
        
        $result = $this->repository->dropPlayerToWaivers(123, 1234567890);
        
        $this->assertTrue($result);
        
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertCount(1, $queries);
        $this->assertStringContainsString('UPDATE ibl_plr', $queries[0]);
        $this->assertStringContainsString('ordinal', $queries[0]);
        $this->assertStringContainsString('1000', $queries[0]);
        $this->assertStringContainsString('droptime', $queries[0]);
        $this->assertStringContainsString('1234567890', $queries[0]);
        $this->assertStringContainsString('WHERE `pid` = 123', $queries[0]);
    }
    
    public function testSignPlayerFromWaiversWithNewContract()
    {
        $this->mockDb->setReturnTrue(true);
        
        $contractData = [
            'cy1' => 103
        ];
        
        $result = $this->repository->signPlayerFromWaivers(
            123,
            'Boston Celtics',
            2,
            $contractData
        );
        
        $this->assertTrue($result);
        
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertCount(1, $queries);
        $this->assertStringContainsString('UPDATE ibl_plr', $queries[0]);
        $this->assertStringContainsString('ordinal', $queries[0]);
        $this->assertStringContainsString('800', $queries[0]);
        $this->assertStringContainsString('cy1', $queries[0]);
        $this->assertStringContainsString('103', $queries[0]);
        $this->assertStringContainsString('cy` = 1', $queries[0]);
        $this->assertStringContainsString('droptime', $queries[0]);
        $this->assertStringContainsString('= 0', $queries[0]);
    }
    
    public function testSignPlayerFromWaiversWithExistingContract()
    {
        $this->mockDb->setReturnTrue(true);
        
        $contractData = []; // No new contract
        
        $result = $this->repository->signPlayerFromWaivers(
            123,
            'Boston Celtics',
            2,
            $contractData
        );
        
        $this->assertTrue($result);
        
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertCount(1, $queries);
        $this->assertStringContainsString('UPDATE ibl_plr', $queries[0]);
        $this->assertStringContainsString('ordinal', $queries[0]);
        $this->assertStringContainsString('800', $queries[0]);
        $this->assertStringNotContainsString('cy1', $queries[0]);
    }
    
    public function testGetWaiverPoolMovesCategoryReturnsID()
    {
        $this->mockDb->setMockData([
            ['catid' => 5]
        ]);
        $this->mockDb->setNumRows(1);
        
        $catID = $this->repository->getWaiverPoolMovesCategory();
        
        $this->assertEquals(5, $catID);
    }
    
    public function testGetWaiverPoolMovesCategoryReturnsNullWhenNotFound()
    {
        $this->mockDb->setNumRows(0);
        
        $catID = $this->repository->getWaiverPoolMovesCategory();
        
        $this->assertNull($catID);
    }
    
    public function testIncrementWaiverPoolMovesCounterExecutesQuery()
    {
        $this->mockDb->setReturnTrue(true);
        
        $result = $this->repository->incrementWaiverPoolMovesCounter();
        
        $this->assertTrue($result);
        
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertCount(1, $queries);
        $this->assertStringContainsString('UPDATE nuke_stories_cat', $queries[0]);
        $this->assertStringContainsString('counter = counter + 1', $queries[0]);
        $this->assertStringContainsString('Waiver Pool Moves', $queries[0]);
    }
    
    public function testCreateNewsStoryExecutesInsertQuery()
    {
        $this->mockDb->setReturnTrue(true);
        
        $result = $this->repository->createNewsStory(
            32,
            'Test Team make waiver cuts',
            'The Test Team cut Test Player to waivers.'
        );
        
        $this->assertTrue($result);
        
        $queries = $this->mockDb->getExecutedQueries();
        $this->assertCount(1, $queries);
        $this->assertStringContainsString('INSERT INTO nuke_stories', $queries[0]);
        $this->assertStringContainsString('Test Team make waiver cuts', $queries[0]);
        $this->assertStringContainsString('The Test Team cut Test Player to waivers.', $queries[0]);
        $this->assertStringContainsString('Associated Press', $queries[0]);
    }
    
    public function testGetTeamTotalSalaryHandlesMissingContractYearField()
    {
        $this->mockDb->setMockData([
            ['cy' => 1], // Missing cy1 field
            ['cy' => 2, 'cy2' => 600]
        ]);
        $this->mockDb->setNumRows(2);
        
        $totalSalary = $this->repository->getTeamTotalSalary('Boston Celtics');
        
        $this->assertEquals(600, $totalSalary);
    }
}
