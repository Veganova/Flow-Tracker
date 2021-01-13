<?php
  require_once "iDrawable.php";

  class CategoryTimedPill {

    public function __construct($activityId, $name, $color, $startTime, $endTime, $duration) {
      $this->activityId = $activityId;
      $this->name = $name;
      $this->color = $color;
      $this->startTime = $startTime;
      $this->endTime = $endTime;
      $this->duration = $duration;
    }

    private function renderContent() {
      ?> 
      <span><?= $this->name ?></span>
      <span class="timer">0:00</span>
      <?php
    }

    public function render() {
      ?>
        <div 
          id="<?= $this->activityId ?>"
          class="pill-timed"
          style="background: <?= $this->color; ?>"
        >
          <?= $this->renderContent(); ?>
        </div>
        <script>
          var activeTimer = {
            start: Date.now(),
            totalPauseDuration: 0, // seconds,
            paused: false,
            pauseStart: null,
            pause: function() {
              this.paused = true;
              this.pauseStart = Date.now();
            },
            unpause: function() {
              this.paused = false;
              this.totalPauseDuration += (Date.now() - this.pauseStart) / 1000;
              this.pauseStart = null;
            },
            formatDate: function () {
              return ("0" + this.hours).slice(-2) + ":" + ("0" + this.minutes).slice(-2) + ":" + ("0" + this.seconds).slice(-2);
            }
          }
          activeTimer.intervalTimer = setInterval(function() {
            if (!activeTimer.paused) {
              var totalSeconds = Math.floor((Date.now() - activeTimer.start) / 1000 - activeTimer.totalPauseDuration);
              activeTimer.hours = Math.floor(totalSeconds / 3600);
              totalSeconds %= 3600;
              activeTimer.minutes = Math.floor(totalSeconds / 60);
              activeTimer.seconds = totalSeconds % 60;

              $(".timer").last().html(activeTimer.formatDate());
            }
          }, 1000); // update about every second
        </script>
      <?php
    }
  }
?>


