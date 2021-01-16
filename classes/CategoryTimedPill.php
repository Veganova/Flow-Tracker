<?php
  function insertCategoryTimedPillScripts() {
?>
  
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
  }

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
      <span duration="<?= $this->duration ?>" class="timer"><?= $this->formatDuration() ?></span>
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
          startDuration: 0,
          totalPauseDuration: 0, // seconds,
          paused: false,
          pauseStart: null,
          instance: null,
          validTimerExists: function() {
            return $(".timer").length > 0
          },
          pause: function() {
            this.paused = true;
            this.pauseStart = Date.now();
            $("#play").css({"display": "block"});
            $("#pause").css({"display": "none"});
          },
          unpause: function(startIfInactive=false) {
            if (this.pauseStart) {
              this.totalPauseDuration += (Date.now() - this.pauseStart) / 1000;
              this.pauseStart = null;
            }

            this.paused = false;
            
            if (startIfInactive && !this.isActive() && this.validTimerExists()) {
              console.log("here")
              this.startNewTimer();
            } 

            $("#play").css({"display": "none"});
            $("#pause").css({"display": "block"});
          },
          togglePause: function() {
            console.log("pause toggled");
            if (this.validTimerExists()) {
              if (this.paused) {
                this.unpause(true);
              } else {
                this.pause();
              }
            } else {
              $("#play").css({"display": "none"});
              $("#pause").css({"display": "none"});
            }
          },
          formatDate: function (totalSeconds) {
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
          getDuration: function() {
            return Math.floor((Date.now() - this.start) / 1000 + this.startDuration - this.totalPauseDuration); 
          },
          updateTime: function() {
            if (!this.paused) {
              let duration = this.getDuration();
              this.instance.html(this.formatDate(duration));
            }
          },
          startNewTimer: function() {
            if (!this.validTimerExists()) return;  // No timer to attach to.
            this.unpause();
            
            this.instance = $(".timer").last();
            this.start = Date.now();
            this.totalPauseDuration = 0;
            this.startDuration = parseInt(this.instance.attr("duration")) || 0;

            // Scroll new timer into view
            $('#timed_pills_container').animate({
                scrollTop: parseInt(this.instance.offset().top)
            }, 2000);

            // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function/call
            this.intervalTimer = setInterval(() => this.updateTime.call(this), 1000); // update about every second
          },
          updateTimePill: function(callback) {
            if(this.isActive()) {
              let request = $.ajax({
                url: "/requests/add_pill.php",
                type: "post",
                data: {
                  updateActivity: {
                    startTime: new Date(this.start).toISOString(),
                    endTime: new Date(Date.now()).toISOString(),
                    duration: this.getDuration(),
                    id: this.instance.parent().attr("id")
                  }
                }
              });

              request.done(function (response, textStatus, jqXHR){
                console.log("Updated the active timer!", response);
                callback();
              });
            }
          }
        }

        $(document).keypress(function(e) {
          if(e.which == 32) { // Spacebar 
            activeTimer.togglePause();
          }
        });
      </script>
    <?php
  }
?>


