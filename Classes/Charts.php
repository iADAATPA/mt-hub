<?php

/**
 * Class Charts
 */
class Charts
{
    private $id = null;
    private $placeHolderWidth = 'auto';
    private $placeHolderHeight = 'auto';
    private $data = null;
    private $labels = null;
    private $dataSets = null;
    private $yAxes = null;
    private $showDoubleAxes = false;
    private $title = null;

    /**
     * Charts constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * Prints chart canvas
     */
    public function printPlaceHolder()
    {
        echo '<canvas id="' . $this->getId() . '" width="' . $this->getPlaceHolderWidth() . '" height="' . $this->getPlaceHolderHeight() . '"></canvas>';
    }

    /**
     * Return background color based on the data set and passed variable
     * @param $i
     * @return array|mixed
     */
    private function printBackgroundColour($i)
    {
        $backgroundColor = [
            'rgba(243,156,18)',
            'rgba(30,126,30)',
            'rgba(123,123,227)',
            'rgba(190,154,233)',
            'rgba(139,50,101)',
            'rgba(139,133,50)',
            'rgba(139,89,50)',
            'rgba(243,156,18)',
            'rgba(30,126,30)',
            'rgba(123,123,227)',
            'rgba(190,154,233)',
            'rgba(139,50,101)',
            'rgba(139,133,50)',
            'rgba(139,89,50)',
            'rgba(243,156,18)',
            'rgba(30,126,30)',
            'rgba(123,123,227)',
            'rgba(190,154,233)',
            'rgba(139,50,101)',
            'rgba(139,133,50)',
            'rgba(139,89,50)',
            'rgba(243,156,18)',
            'rgba(30,126,30)',
            'rgba(123,123,227)',
            'rgba(190,154,233)',
            'rgba(139,50,101)',
            'rgba(139,133,50)',
            'rgba(139,89,50)'
        ];

        if (!empty($this->getData()) && is_array($this->getData())) {
            if (count($this->getData()) > 1) {
                return $backgroundColor[$i];
            }
        }

        return $backgroundColor;
    }

    /**
     * Prints js code for the chart
     */
    public function printScript()
    {
        $this->setChartDataAndOptions();
        ?>
        <script>
            var chart<?php echo $this->getId(); ?> = document.getElementById("<?php echo $this->getId(); ?>");

            var data = {
                labels: <?php echo $this->getLabels(); ?>,
                datasets: <?php echo $this->getDataSets(); ?>
            };

            var options = {
                legend: {
                    display: true,
                    position: "bottom"
                },
                title: <?php echo $this->getTitle(); ?>,
                scales: {
                    xAxes: [{
                        barPercentage: 1,
                        categoryPercentage: 0.6
                    }],
                    yAxes: <?php echo $this->getYAxes(); ?>
                }
            };

            Chart.defaults.global.defaultFontFamily = "'Open Sans', 'Trebuchet MS', Arial, Helvetica, sans-serif";
            var barChart = new Chart(chart<?php echo $this->getId(); ?>, {
                type: 'bar',
                data: data,
                options: options
            });
        </script>
        <?php
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = trim($id);
    }

    /**
     * @return string
     */
    public function getPlaceHolderWidth()
    {
        return $this->placeHolderWidth;
    }

    /**
     * @param string $placeHolderWidth
     */
    public function setPlaceHolderWidth($placeHolderWidth)
    {
        $this->placeHolderWidth = $placeHolderWidth;
    }

    /**
     * @return string
     */
    public function getPlaceHolderHeight()
    {
        return $this->placeHolderHeight;
    }

    /**
     * @param string $placeHolderHeight
     */
    public function setPlaceHolderHeight($placeHolderHeight)
    {
        $this->placeHolderHeight = $placeHolderHeight;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return null
     */
    private function getLabels()
    {
        $labels = $this->labels;
        if (empty($labels)) {
            $labels = [];
        }

        return json_encode($labels);
    }

    /**
     * @param null $labels
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    /**
     * @return null
     */
    private function getDataSets()
    {
        if (empty($this->dataSets)) {
            return json_encode([]);
        }

        return $this->dataSets;
    }

    /**
     * @param null $dataSets
     */
    private function setDataSets($dataSets)
    {
        $this->dataSets = $dataSets;
    }

    /**
     * @return null
     */
    private function getYAxes()
    {
        if (empty($this->yAxes)) {
            return json_encode([]);
        }

        return $this->yAxes;
    }

    /**
     * @param null $yAxes
     */
    private function setYAxes($yAxes)
    {
        $this->yAxes = $yAxes;
    }

    /**
     * @return bool
     */
    public function isShowDoubleAxes()
    {
        return $this->showDoubleAxes;
    }

    /**
     * @param bool $showDoubleAxes
     */
    public function setShowDoubleAxes($showDoubleAxes)
    {
        $this->showDoubleAxes = $showDoubleAxes;
    }

    /**
     * @return null
     */
    private function getTitle()
    {
        $title = [
            "disaply" => false
        ];

        if (!empty($this->title)) {
            $title = [
                "display" => true,
                "text" => $this->title
            ];
        }

        return json_encode($title);
    }

    /**
     * @param null $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Set chart data and option
     */
    private function setChartDataAndOptions()
    {
        $data = $this->getData();

        // Set labels from the data
        if (!empty($data) && is_array($data)) {
            $dataSets = [];
            $yAxes = [];
            $i = 0;
            $yAxesPosition = "left";
            foreach ($data as $label => $dataSet) {
                $dataSets[$i] = [
                    "label" => $label,
                    "data" => $dataSet,
                    "borderWidth" => 0,
                    "backgroundColor" => $this->printBackgroundColour($i)
                ];

                if ($this->isShowDoubleAxes()) {
                    $dataSets[$i]["yAxisID"] = preg_replace('/\s+/', '', $label);
                }

                if (($this->isShowDoubleAxes() && $i <= 1) || $i == 0) {
                    $yAxes[$i] = [
                        "position" => $yAxesPosition,
                        //"type" => "logarithmic",
                        "ticks" => [
                            "beginAtZero" => true,
                            "callback" => "####function(value, index, values) { if (Math.floor(value) === value) { return value; } } ####"
                        ]
                    ];

                    if ($this->isShowDoubleAxes()) {
                        $yAxes[$i]["id"] = preg_replace('/\s+/', '', $label);
                    }

                    $yAxesPosition = "right";
                }

                $i++;
            }

            $this->setDataSets(json_encode($dataSets));
            // We check if there are any quotes that need to be removed before setting the axes
            $this->setYAxes(str_replace(['"####', '####"'], " ", json_encode($yAxes)));
        }
    }
}
