<script>
  function addTimedPill(pillId) {
    let container = document.getElementById("timed_pills_container");

    let addPillData = {
      categoryId: pillId,
      sessionId: new URLSearchParams(window.location.search).get("session"),
    };

    if(activeTimer.isActive()) {
      console.log("activeTmer is defined so an updateActivity will be set");
      console.log(activeTimer);
      // the variable is defined
      addPillData["updateActivity"] = {
        startTime: new Date(activeTimer.start).toISOString(),
        endTime: new Date(Date.now()).toISOString(),
        duration: activeTimer.duration,
        id: activeTimer.instance.parent().attr("id")
      };
    }

    let request = $.ajax({
      url: "/requests/add_pill.php",
      type: "post",
      data: { addPill: addPillData }
    })

    request.done(function (response, textStatus, jqXHR){
        if (!activeTimer.isActive()) {
          // if this is the first activity pill being added, then show the play pause.
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