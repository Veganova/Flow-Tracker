<script>
  function removeTimedPill(pillId) {
    let request = $.ajax({
      url: "/requests/remove_pill.php",
      type: "post",
      data: { removePill: pillId }
    })

    request.done(function (response, textStatus, jqXHR){
        if (typeof activeTimer !== 'undefined') {
          // the variable is defined
          clearInterval(activeTimer.intervalTimer);
        } 
        
        // $("#pause").css({"display": "block"});
        

        $(container).remove();
        container.dispatchEvent(new CustomEvent('scroll'));

        startNewTimer();
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // Log the error to the console
        console.error(
            "The following error occurred: "+
            textStatus, errorThrown
        );
    });
  }

</script>

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

    private function formatDuration() {
      $totalSeconds = $this->duration;
      $hours = sprintf('%02d', floor($totalSeconds / 3600));
      $totalSeconds %= 3600;
      $minutes = sprintf('%02d', floor($totalSeconds / 60));
      $seconds = sprintf('%02d', $totalSeconds % 60);
      
      return "$hours:$minutes:$seconds";
    }

    private function renderContent() {
      ?> 
      <span><?= $this->name ?></span>
      <span class="timer"><?= $this->formatDuration() ?></span>
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
      <?php
    }
  }


  function initiateTimer() {
    ?>
      <script>
        var activeTimer = {
          start: Date.now(),
          duration: 0,
          totalPauseDuration: 0, // seconds,
          paused: false,
          pauseStart: null,
          instance: $(".timer").last(),
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
            let totalSeconds = this.duration;
            const hours = Math.floor(totalSeconds / 3600);
            totalSeconds %= 3600;
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            return ("0" + hours).slice(-2) + ":" + ("0" + minutes).slice(-2) + ":" + ("0" + seconds).slice(-2);
          },
          isActive: function() {
            return this.instance && this.instance.length;
          },
          clearTimer: function() {
            if(this.isActive()) {
              clearInterval(this.intervalTimer);
              this.instance = null;
            }
          },
          updateTime: function() {
            if (!this.paused) {
              this.duration  = Math.floor((Date.now() - this.start) / 1000 - this.totalPauseDuration);
              this.instance.html(this.formatDate());
            }
          },
          startNewTimer: function() {
            this.instance = $(".timer").last();
            // Set this to this cause this isn't known if called from setInterval..lol
            // fiiine here you go: 
            // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function/call
            this.intervalTimer = setInterval(() => this.updateTime.call(this), 1000); // update about every second
          }
        }
      </script>
    <?php
  }
?>


