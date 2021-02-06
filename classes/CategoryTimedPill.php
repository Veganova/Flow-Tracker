<?php
  function insertCategoryTimedPillScripts() {
?>
  
  <script>
    function removeTimedPill(activityId) {
      let request = $.ajax({
        url: "/requests/pill.php",
        type: "post",
        data: { 
          removeActivity: {
            id: activityId 
          }
        }
      })

      request.done(function (response, textStatus, jqXHR){
        if (response) {
          let containerId = "activity-" + activityId;
          $("#"+containerId).remove();
          if (activeTimer.getActivityId() === parseInt(activityId)) {
            activeTimer.clearTimer();
            // activeTimer.startNewTimer();
          }
        }
      });

      request.fail(function (jqXHR, textStatus, errorThrown){
          console.error( "The following error occurred: "+textStatus, errorThrown);
      });
    }

    function formatDuration(totalSeconds, showSeconds=true) {
      const hours = Math.floor(totalSeconds / 3600);
      totalSeconds %= 3600;
      const minutes = Math.floor(totalSeconds / 60);
      const seconds = totalSeconds % 60;

      let result = ("0" + hours).slice(-2) + ":" + ("0" + minutes).slice(-2);
      if (showSeconds) {
        result +=  ":" + ("0" + seconds).slice(-2);
      }

      return result;
    }

    function formatTime(date=null) {
      date = date ? new Date(date) : new Date();
      let options = { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };

      return date.toLocaleDateString("en-US", options);
    }

    function hideAllButLastTime() {
      // Last container's time and cancel will be always visible
      $(".activity-container").find(".toggle-click").hide();
      $(".activity-container").last().find(".toggle-click").css({display: "flex"});
    }

    function showCancelAndTime(containerId) {
      let container = $("#"+containerId);
      if (container.find(".cancel:visible").length > 0) {
        container.find(".toggle-click").hide();
      } else {
        container.find(".toggle-click").css({display: "flex"});
      }
    }
  </script>
<?php
  }

  require_once "iDrawable.php";

  class CategoryTimedPill {

    public function __construct($activityId, $name, $color, $startTime, $endTime, $duration) {
      // Timezone
      date_default_timezone_set('UTC');
      $this->activityId = htmlspecialchars($activityId);
      $this->name = htmlspecialchars($name);
      $this->color = htmlspecialchars($color);
      $this->startTime = htmlspecialchars($startTime);
      $this->endTime = htmlspecialchars($endTime);
      $this->duration = htmlspecialchars($duration);
    }

    private function getContainerId() {
      return "activity-".$this->activityId;
    }

    
    private function insertTime() {
      $epoch = $this->endTime ? strtotime($this->endTime) * 1000 : null;
      ?>
      <script>
        $("#<?= $this->getContainerId() ?>").find(".full-time").html(
          formatTime(<?= $epoch ?>)
        );
      </script>
      <?php
    }

    private function insertActivityDuration() {
      ?>
      <script>
        $("#<?= $this->getContainerId() ?>").find(".timer").html(
          formatDuration(<?= $this->duration ?>, true)
        );
      </script>
      <?php
    }

    private function renderContent() {
      ?> 
      <span><?= $this->name ?></span>
      <span activityId="<?= $this->activityId ?>" duration="<?= $this->duration ?>" class="timer">0:00:00</span>
      <?php
    }

    public function render() {
      global $ROOT;
      ?>
        <div id="<?= $this->getContainerId() ?>" class="activity-container">
          <div class="pill-row">
            <div class="pill-timed" style="background: <?= $this->color; ?>" onclick="showCancelAndTime('<?= $this->getContainerId() ?>')">
              <?= $this->renderContent(); ?>
            </div>
            <div class="cancel toggle-click" onclick="removeTimedPill('<?= $this->activityId ?>')">
              <?= file_get_contents($ROOT."assets/cancel.svg"); ?>
            </div>
          </div>
          <div class="full-time-container toggle-click">
            <hr>
            <div class="full-time"></div>
          </div>
        </div>
      <?php
      $this->insertActivityDuration();
      $this->insertTime();
      ?><script>hideAllButLastTime();</script><?php
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
          getActivityId: function() {
            return this.instance ? parseInt(this.instance.attr("activityId")) : -1;
          },
          getContainerId: function() {
            return "activity-" + this.getActivityId();
          },
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
                this.updateTimePill(()=>{});
                this.pause();
              }
            } else {
              $("#play").css({"display": "none"});
              $("#pause").css({"display": "none"});
            }
          },
          isActive: function() {
            return this.instance && this.instance.length;
          },
          clearTimer: function() {
            if(this.isActive()) {
              clearInterval(this.intervalTimer);
              this.instance = null;
              this.pause();
            }
          },
          getDuration: function() {
            return Math.floor((Date.now() - this.start) / 1000 + this.startDuration - this.totalPauseDuration); 
          },
          updateTime: function() {
            if (!this.paused) {
              let duration = this.getDuration();
              this.instance.html(formatDuration(duration));
              $("#" + this.getContainerId()).find(".full-time").html(formatTime());
            }
          },
          startNewTimer: function() {
            if (!this.validTimerExists()) return;  // No timer to attach to.
            this.unpause();
            
            this.instance = $(".timer").last();
            this.start = Date.now();
            this.totalPauseDuration = 0;
            this.startDuration = parseInt(this.instance.attr("duration")) || 0;

            hideAllButLastTime();

            // Scroll new timer into view
            if(!$('#timed_pills_container').is(':animated') ) {
              $('#timed_pills_container').animate({
                  scrollTop: parseInt($('#timed_pills_container').get(0).scrollHeight) //parseInt(this.instance.offset().top)
              }, 2000);              
            }
            // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function/call
            this.intervalTimer = setInterval(() => this.updateTime.call(this), 1000); // update about every second
          },
          updateTimePill: function(callback) {
            if(this.isActive()) {
              let request = $.ajax({
                url: "/requests/pill.php",
                type: "post",
                data: {
                  updateActivity: {
                    startTime: new Date(this.start).toISOString(),
                    endTime: new Date(Date.now()).toISOString(),
                    duration: this.getDuration(),
                    id: this.getActivityId()
                  }
                }
              });

              request.done((response, textStatus, jqXHR) => {
                console.log("Updated the active timer!", response);
                callback();
              });
            } else {
              callback();
            }
          }
        }

        $(document).keypress(function(e) {
          let inputFocused = document.activeElement.tagName.toLocaleLowerCase() == "input";
          if(!inputFocused && e.which == 32) { // Spacebar 
            activeTimer.togglePause();
          }
        });
      </script>
    <?php
  }
?>


