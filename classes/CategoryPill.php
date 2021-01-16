<?php
  function insertCategoryPillScripts() {
  ?>
  <script>
    // TODO figure out how to save on page close
    window.addEventListener('onunload', function (e) {
        e.preventDefault();
        e.returnValue = '';

        console.log("go for the updated!!");
        activityTimer.updateTimePill();
    });

    function addTimedPill(pillId) {
      console.log("adding timed pill", pillId);
      let container = document.getElementById("timed_pills_container");

      const data = { 
        addPill: {
          categoryId: pillId,
          sessionId: new URLSearchParams(window.location.search).get("session"),
        }
      }

      if(activeTimer.isActive()) {
        // the variable is defined
        data["updateActivity"] = {
          startTime: new Date(activeTimer.start).toISOString(),
          endTime: new Date(Date.now()).toISOString(),
          duration: activeTimer.duration,
          id: activeTimer.instance.parent().attr("id")
        };
      }

      let request = $.ajax({
        url: "/requests/add_pill.php",
        type: "post",
        data: data
      })

      request.done(function (response, textStatus, jqXHR){
          if (!activeTimer.validTimerExists()) {
            // if this is the first activity pill being added, then show the pause.
            $("#pause").css({"display": "block"});
          }

          $(container).append(response);
          container.dispatchEvent(new CustomEvent('scroll'));

          activeTimer.clearTimer();
          activeTimer.startNewTimer();
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

  // Once clicked will add timed pills to the page
  class CategoryPill implements iDrawable {
    private function renderContent() {
      return $this->name;
    }

    public function render() {
      ?>
        <div 
          class="pill"
          style="background: <?= $this->color; ?>"
          onclick="addTimedPill(<?= $this->id ?>)"
        >
          <?= $this->renderContent(); ?>
        </div>
      <?php
    }
  }
?>