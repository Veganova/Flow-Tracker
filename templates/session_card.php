<script>

  function renderChart(sessionId, labels, durations, colors, small) {

    // console.log(labels);
    // console.log(durations);
    // console.log(colors);
    // console.log("=========")
    let container = document.getElementById(sessionId);
    let ctx = container.querySelector(".chart").getContext("2d");
    const cfg = {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          borderColor: labels.length <= 1 ? "rgba(0,0,0,0)" : '#3A3A3A',
          borderWidth: small ? 2 : 5,
          label: 'My First Dataset',
          data: durations,
          backgroundColor: colors,
          hoverOffset: 20
        }]
      },
      options: {
        cutoutPercentage: small ? 0 : 40,
        maintainAspectRatio: false,
        animation: {
          animateRotate: true,
          // animateScale: true
        },
        legend: {
          display: false
        },
        tooltips: {
          enabled: false,
          custom: function(tooltip) {
            let tooltipEl = container.querySelector(".chartjs-tooltip");

            // Hide if no tooltip
            if (tooltip.opacity === 0) {
              tooltipEl.style.opacity = 0;
              return;
            }

            // Set caret Position
            tooltipEl.classList.remove('above', 'below', 'no-transform');
            if (tooltip.yAlign) {
              tooltipEl.classList.add(tooltip.yAlign);
            } else {
              tooltipEl.classList.add('no-transform');
            }

            function getBody(bodyItem) {
              let line = bodyItem.lines[0];
              let category = line.split(":")[0];
              let totalSeconds = parseFloat(line.split(":")[1]);
              let hours = Math.floor(totalSeconds / 3600);
              totalSeconds = totalSeconds % 3600;
              let minutes = Math.floor(totalSeconds / 60);
              let seconds = totalSeconds % 60;
              let formattedTime = hours + "h " + minutes + "m " + seconds + "s";
              return `<span>${category}</span><div>${formattedTime}</div>`;
            }

            // Set Text
            if (tooltip.body) {
              let titleLines = tooltip.title || [];
              let bodyLines = tooltip.body.map(getBody);

              let innerHtml = '<thead>';

              titleLines.forEach(function(title) {
                innerHtml += '<tr><th>' + title + '</th></tr>';
              });
              innerHtml += '</thead><tbody>';

              bodyLines.forEach(function(body, i) {
                let colors = tooltip.labelColors[i];
                let style = 'background:' + colors.backgroundColor;
                style += '; border-color:' + colors.borderColor;
                style += '; border-width: 2px';
                let span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';
                innerHtml += '<tr><td>' + span + body + '</td></tr>';
              });
              innerHtml += '</tbody>';

              let tableRoot = tooltipEl.querySelector('table');
              tableRoot.innerHTML = innerHtml;
            }

            let positionY = this._chart.canvas.offsetTop;
            let positionX = this._chart.canvas.offsetLeft;

            // Display, position, and set styles for font
            tooltipEl.style.opacity = 1;
            console.log(tooltipEl);
            console.log(tooltip);
            tooltipEl.style.left = positionX + tooltip.caretX + 'px';
            tooltipEl.style.top = positionY + tooltip.caretY + 'px';
            tooltipEl.style.fontFamily = tooltip._bodyFontFamily;
            tooltipEl.style.fontSize = tooltip.bodyFontSize;
            tooltipEl.style.fontStyle = tooltip._bodyFontStyle;
            tooltipEl.style.padding = tooltip.yPadding + 'px ' + tooltip.xPadding + 'px';
          }
        }
      }
    };
    console.log("here!");
    let doughnut = new Chart(ctx, cfg);
  }

  function preventClick(e) {
    e.preventDefault();
    e.stopPropagation();
  }
 
</script>

<?php
  function formatDate($date) {
    $phpdate = strtotime( $date );
    return date( 'Y/m/d \a\t H:i', $phpdate );
  }


  function pad($n) {
    $n = explode('.', (string)$n);

    if (2 === count($n)) {
        return sprintf("%02d.%d\n", $n[0], $n[1]);    
    }

    return sprintf("%02d", $n[0]);    
  }

  function retrieveLabels($activities) {
    $labels = [];

    foreach($activities as $activity) {
      $labels[$activity->name] = 1;
    }

    return array_keys($labels);
  }

  // Done by Categories
  function activityDurationsAndColors($activities, $labels) {
    $indexMapping = array_flip($labels);
    $durations = array_fill(0, count($labels), 0);
    $colors = array_fill(0, count($labels), "black");
    foreach($activities as $activity) {
      $i = $indexMapping[$activity->name];
      $durations[$i] += ($activity->duration);
      $colors[$i] = $activity->color;
    }

    return [$durations, $colors];
  }

  function totalDuration($activities) {
    $total = 0;
    foreach($activities as $activity) {
      $total += $activity->duration;
    }
    // echo $total;
    $hours = floor($total / 3600);
    $total = $total % 3600;
    $minutes = floor($total / 60);
    $seconds = $total % 60;
    // return pad($hours).":".pad($minutes).":".pad($seconds);
    return $hours."h ".$minutes."m";
  }

  function domID($session) {
    return "session-".$session->id;
  }

  // One row showing session info
  function renderSessionDetails($session, $activities, $small=true) {
    // Sort sessions by the ones that have the most recently added(/updated) activity 
    // Each activity with the percentage of it's 

    $labels = retrieveLabels($activities);
    $labelsEnc = json_encode($labels);
    $result = activityDurationsAndColors($activities, $labels);
    $durations = json_encode($result[0]);
    $colors = json_encode($result[1]);
?>
  <a href="/pages/session.php?session=<?= $session->id ?>"id="<?= domID($session) ?>" class="session-details">
    <div class="row-el date">
      <?= formatDate($session->updated_at) ?>
    </div>
    <div class="row-el duration">
      <?= totalDuration($activities) ?> 
    </div>

    <div class="row-el">
      <?= count($activities) ?>
    </div>

    <div class="row-el">
      <div class="chart-container" onclick="preventClick(event)">
        <canvas class="chart"></canvas>
        <div class="chartjs-tooltip">
          <table></table>
        </div>
      </div>
    </div>
  
  </a> 
  <script>
    renderChart(
      "<?= domID($session) ?>", 
      JSON.parse('<?= $labelsEnc ?>') , 
      JSON.parse('<?= $durations ?>'), 
      JSON.parse('<?= $colors ?>'),
      <?= $small ?>
    );
  </script>

  
<?php
  }

  function renderTableColumnNames($tableColumns) {
  ?>
    <div class="column-names-row">
  <?php
    foreach($tableColumns as $tableColumn) {
  ?>
        <div class="row-el column-name">
          <?= $tableColumn ?>
        </div>
  <?php
    }
  ?>
    </div>
  <?php
  }
?>