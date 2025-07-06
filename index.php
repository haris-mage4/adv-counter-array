<?php

declare(strict_types=1);

/**
 * Advanced Array Counter - Professional Implementation
 *
 * This class provides multiple approaches to counting array elements
 * with performance optimization, error handling, and extensibility.
 */
class AdvancedArrayCounter
{
    private const NUMBER_NAMES = [
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten'
    ];

    private array $data;
    private array $counts = [];
    private ?array $statistics = null;

    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * Set the data array with validation
     */
    public function setData(array $data): self
    {
        $this->validateInput($data);
        $this->data = $data;
        $this->resetCache();
        return $this;
    }

    /**
     * Validate input data
     */
    private function validateInput(array $data): void
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Data array cannot be empty');
        }

        foreach ($data as $value) {
            if (!is_int($value) || $value < 0) {
                throw new InvalidArgumentException('All values must be non-negative integers');
            }
        }
    }

    /**
     * Reset internal cache
     */
    private function resetCache(): void
    {
        $this->counts = [];
        $this->statistics = null;
    }

    /**
     * Method 2: Functional approach using array functions
     */
    public function countFunctional(): array
    {
        return array_reduce($this->data, function ($carry, $item) {
            $carry[$item] = ($carry[$item] ?? 0) + 1;
            return $carry;
        }, []);
    }

    /**
     * Method 3: Object-oriented approach with iterator
     */
    public function countIterative(): array
    {
        $iterator = new ArrayIterator($this->data);
        $counts = [];

        foreach ($iterator as $value) {
            $counts[$value] = ($counts[$value] ?? 0) + 1;
        }

        ksort($counts);
        return $counts;
    }

    /**
     * Export results in multiple formats
     */
    public function export(string $format = 'array'): mixed
    {
        $data = ['counts' => $this->getFormattedCounts(), 'statistics' => $this->getStatistics()];

        return match (strtolower($format)) {
            'json' => json_encode($data, JSON_PRETTY_PRINT),
            'xml' => $this->toXml($data),
            'csv' => $this->toCsv($data['counts']),
            'array' => $data,
            default => throw new InvalidArgumentException("Unsupported format: $format")
        };
    }

    /**
     * Get formatted output with number names
     */
    public function getFormattedCounts(): array
    {
        $counts = $this->countOptimized();
        $formatted = [];

        foreach ($counts as $number => $count) {
            $name = self::NUMBER_NAMES[$number] ?? "Number $number";
            $formatted[$name] = $count;
        }

        return $formatted;
    }

    /**
     * Method 1: Optimized counting using array_count_values
     */
    public function countOptimized(): array
    {
        if (empty($this->counts)) {
            $this->counts = array_count_values($this->data);
            ksort($this->counts);
        }
        return $this->counts;
    }

    /**
     * Generate comprehensive statistics
     */
    public function getStatistics(): array
    {
        if ($this->statistics === null) {
            $counts = $this->countOptimized();
            $this->statistics = ['total_elements' => count($this->data), 'unique_values' => count($counts), 'most_frequent' => $this->getMostFrequent($counts), 'least_frequent' => $this->getLeastFrequent($counts), 'frequency_distribution' => $this->getFrequencyDistribution($counts), 'entropy' => $this->calculateEntropy($counts)];
        }

        return $this->statistics;
    }

    /**
     * Get most frequent value(s)
     */
    private function getMostFrequent(array $counts): array
    {
        $maxCount = max($counts);
        return array_keys($counts, $maxCount);
    }

    /**
     * Get least frequent value(s)
     */
    private function getLeastFrequent(array $counts): array
    {
        $minCount = min($counts);
        return array_keys($counts, $minCount);
    }

    /**
     * Calculate frequency distribution
     */
    private function getFrequencyDistribution(array $counts): array
    {
        $total = array_sum($counts);
        $distribution = [];

        foreach ($counts as $value => $count) {
            $distribution[$value] = ['count' => $count, 'percentage' => round(($count / $total) * 100, 2)];
        }

        return $distribution;
    }

    /**
     * Calculate Shannon entropy
     */
    private function calculateEntropy(array $counts): float
    {
        $total = array_sum($counts);
        $entropy = 0;

        foreach ($counts as $count) {
            if ($count > 0) {
                $probability = $count / $total;
                $entropy -= $probability * log($probability, 2);
            }
        }

        return round($entropy, 4);
    }

    /**
     * Convert to XML format
     */
    private function toXml(array $data): string
    {
        $xml = new SimpleXMLElement('<ArrayCounterResults/>');

        $countsNode = $xml->addChild('Counts');
        foreach ($data['counts'] as $name => $count) {
            $item = $countsNode->addChild('Item');
            $item->addAttribute('name', $name);
            $item->addAttribute('count', (string)$count);
        }

        $statsNode = $xml->addChild('Statistics');
        foreach ($data['statistics'] as $key => $value) {
            if (!is_array($value)) {
                $statsNode->addChild($key, is_string($value) ? $value : (string)$value);
            }
        }

        return $xml->asXML();
    }

    /**
     * Convert to CSV format
     */
    private function toCsv(array $counts): string
    {
        $csv = "Number,Count\n";
        foreach ($counts as $name => $count) {
            $csv .= "\"$name\",$count\n";
        }
        return $csv;
    }

    /**
     * Display results in a formatted table
     */
    public function displayResults(): void
    {
        $counts = $this->getFormattedCounts();
        $stats = $this->getStatistics();

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "ADVANCED ARRAY COUNTER RESULTS\n";
        echo str_repeat("=", 60) . "\n\n";

        echo "📊 COUNT RESULTS:\n";
        echo str_repeat("-", 30) . "\n";
        foreach ($counts as $name => $count) {
            printf("%-15s: %d\n", $name, $count);
        }

        echo "\n STATISTICS:\n";
        echo str_repeat("-", 30) . "\n";
        printf("Total Elements  : %d\n", $stats['total_elements']);
        printf("Unique Values   : %d\n", $stats['unique_values']);
        printf("Most Frequent   : %s\n", implode(', ', $stats['most_frequent']));
        printf("Least Frequent  : %s\n", implode(', ', $stats['least_frequent']));
        printf("Entropy         : %.4f\n", $stats['entropy']);

        echo "\n🔍 FREQUENCY DISTRIBUTION:\n";
        echo str_repeat("-", 40) . "\n";
        foreach ($stats['frequency_distribution'] as $value => $data) {
            printf("Value %d: %d occurrences (%.2f%%)\n", $value, $data['count'], $data['percentage']);
        }

        echo "\n⚡ PERFORMANCE BENCHMARK:\n";
        echo str_repeat("-", 40) . "\n";
        $benchmarks = $this->benchmark();
        foreach ($benchmarks as $method => $time) {
            printf("%-20s: %.3f ms\n", $method, $time);
        }

        echo "\n" . str_repeat("=", 60) . "\n";
    }

    /**
     * Performance benchmark
     */
    public function benchmark(): array
    {
        $methods = ['countOptimized', 'countFunctional', 'countIterative'];
        $results = [];

        foreach ($methods as $method) {
            $start = microtime(true);
            $this->$method();
            $end = microtime(true);
            $results[$method] = ($end - $start) * 1000; // Convert to milliseconds
        }

        return $results;
    }
}

// Usage Examples and Demonstrations
try {
    // Original data
    $originalData = [0, 3, 2, 4, 8, 0, 4, 8, 2, 4];

    // Create counter instance
    $counter = new AdvancedArrayCounter($originalData);

    // Display comprehensive results
    $counter->displayResults();

    // Export in different formats
    echo "\n📄 JSON Export:\n";
    echo $counter->export('json');

    echo "\n\n📊 CSV Export:\n";
    echo $counter->export('csv');

    // Demonstrate with different datasets
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "TESTING WITH DIFFERENT DATASETS\n";
    echo str_repeat("=", 60) . "\n";

    $testDatasets = ['Small Dataset' => [1, 2, 3, 1, 2, 1], 'Large Dataset' => array_merge(array_fill(0, 100, 5), array_fill(0, 50, 3), array_fill(0, 25, 7)), 'Edge Case' => [0, 0, 0, 0, 0]];

    foreach ($testDatasets as $name => $data) {
        echo "\n🧪 Testing: $name\n";
        $counter->setData($data);
        $stats = $counter->getStatistics();
        printf("Elements: %d, Unique: %d, Entropy: %.4f\n", $stats['total_elements'], $stats['unique_values'], $stats['entropy']);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Factory pattern for different counter types
class CounterFactory
{
    public static function create(string $type, array $data = []): AdvancedArrayCounter
    {
        return match ($type) {
            'basic' => new AdvancedArrayCounter($data),
            'optimized' => new class($data) extends AdvancedArrayCounter {
                public function __construct(array $data = [])
                {
                    parent::__construct($data);
                    // Additional optimizations could be added here
                }
            },
            default => throw new InvalidArgumentException("Unknown counter type: $type")
        };
    }
}

// Demonstrate factory pattern
echo "\n Factory Pattern Demo:\n";
$factoryCounter = CounterFactory::create('basic', [1, 2, 3, 1, 2, 1]);
echo "Factory created counter with " . count($factoryCounter->countOptimized()) . " unique values\n";

?>
