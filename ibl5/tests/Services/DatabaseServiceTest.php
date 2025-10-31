<?php

use PHPUnit\Framework\TestCase;
use Services\DatabaseService;

class DatabaseServiceTest extends TestCase
{
    public function testSafeHtmlOutputWithApostrophe()
    {
        // Simulating data stored in database with addslashes()
        $dbValue = "Jermaine O\\'Neal";
        
        $result = DatabaseService::safeHtmlOutput($dbValue);
        
        // Should remove backslash and properly encode for HTML
        // Note: ENT_HTML5 encodes apostrophes as &apos; (both &apos; and &#039; are valid)
        $this->assertEquals("Jermaine O&apos;Neal", $result);
    }

    public function testSafeHtmlOutputWithDoubleQuote()
    {
        // Simulating data stored in database with addslashes()
        $dbValue = 'John \\"The Rock\\" Johnson';
        
        $result = DatabaseService::safeHtmlOutput($dbValue);
        
        // Should remove backslash and properly encode for HTML
        $this->assertEquals("John &quot;The Rock&quot; Johnson", $result);
    }

    public function testSafeHtmlOutputWithMultipleSpecialChars()
    {
        // Simulating data stored in database with addslashes()
        $dbValue = "O\\'Brien & D\\'Angelo";
        
        $result = DatabaseService::safeHtmlOutput($dbValue);
        
        // Should handle both apostrophes and ampersands
        // Note: ENT_HTML5 encodes apostrophes as &apos;
        $this->assertEquals("O&apos;Brien &amp; D&apos;Angelo", $result);
    }

    public function testSafeHtmlOutputWithHTMLTags()
    {
        // Simulating malicious input stored in database
        $dbValue = "<script>alert(\\'xss\\')</script>";
        
        $result = DatabaseService::safeHtmlOutput($dbValue);
        
        // Should encode HTML tags and handle escaped quotes
        // Note: ENT_HTML5 encodes apostrophes as &apos;
        $this->assertEquals("&lt;script&gt;alert(&apos;xss&apos;)&lt;/script&gt;", $result);
    }

    public function testSafeHtmlOutputForHtmlAttributeWithApostrophe()
    {
        // Simulating data stored in database with addslashes()
        $dbValue = "Jermaine O\\'Neal";
        
        $result = DatabaseService::safeHtmlOutput($dbValue);
        
        // Should be safe for use in HTML attributes
        // Note: ENT_HTML5 encodes apostrophes as &apos;
        $this->assertEquals("Jermaine O&apos;Neal", $result);
        
        // Verify it's safe in an HTML attribute context
        $html = "<input type='hidden' value='$result'>";
        $this->assertStringNotContainsString("\\'", $html);
        $this->assertStringNotContainsString("'Neal'", $html);
    }

    public function testSafeHtmlOutputWithNoEscaping()
    {
        // Plain text without special characters
        $dbValue = "John Smith";
        
        $result = DatabaseService::safeHtmlOutput($dbValue);
        
        $this->assertEquals("John Smith", $result);
    }

    public function testSafeHtmlOutputWithBackslashBeforeN()
    {
        // Edge case: backslash before 'n' (not a newline escape)
        $dbValue = "Player\\nName";
        
        $result = DatabaseService::safeHtmlOutput($dbValue);
        
        // stripslashes will remove single backslash
        $this->assertEquals("PlayernName", $result);
    }

    public function testSafeHtmlOutputPreservesUnicode()
    {
        // UTF-8 characters should be preserved
        $dbValue = "José García";
        
        $result = DatabaseService::safeHtmlOutput($dbValue);
        
        $this->assertEquals("José García", $result);
    }

    public function testSafeHtmlOutputInActualHTML()
    {
        // Real-world scenario: name in hidden input
        $dbValue = "Shaquille O\\'Neal";
        $safeName = DatabaseService::safeHtmlOutput($dbValue);
        
        // Construct HTML like DepthChartView does
        $html = "<input type=\"hidden\" name=\"Name1\" value=\"$safeName\">";
        
        // Verify the HTML is well-formed
        // Note: ENT_HTML5 encodes apostrophes as &apos;
        $this->assertStringContainsString('value="Shaquille O&apos;Neal"', $html);
        $this->assertStringNotContainsString("\\'", $html);
    }
}
