<?php

use PHPUnit\Framework\TestCase;
use Xola\ReportWriterBundle\Service\CSVWriter;

class CSVWriterTest extends TestCase
{
    /** @var CSVWriter */
    private $writer;
    private $testfilename;

    protected function setUp(): void
    {
        $this->writer = $this->buildService();
        $this->testfilename = uniqid() . ".csv";
        $this->writer->setup($this->testfilename);
    }

    protected function tearDown(): void
    {
        $this->writer->finalize();
        unlink($this->testfilename);
    }

    private function buildService($params = [])
    {
        $defaults = ['logger' => $this->getMockBuilder('Psr\Log\LoggerInterface')->disableOriginalConstructor()->getMock()];
        $params = array_merge($defaults, $params);

        return new CSVWriter($params['logger']);
    }

    public function testShouldWriteRowToFile()
    {
        $this->writer->writeRow(['a', 'b', 'c,', 'd']);
        $this->writer->writeRow(['e', 'f', 'g', 'h']);

        $contents = file_get_contents($this->testfilename);
        $expected = "a,b,\"c,\",d\ne,f,g,h\n";
        $this->assertEquals($expected, $contents);
    }
}
