<script>
  function isOverflown(el) {
    // No pixels to scroll at bottom
    var parent = $(el).parent();
    if (el.scrollHeight <= el.clientHeight) {
      parent.find(".border-shadows-bottom").css({"display": "none"});
      parent.find(".border-shadows-top").css({"display": "none"});
    } else  {
      parent.find(".border-shadows-bottom").css({"display": "block"});
      parent.find(".border-shadows-top").css({"display": "block"});
      
      if (el.scrollHeight - el.clientHeight == el.scrollTop) {
        // console.log("hide bottom");
        parent.find(".border-shadows-bottom").css({"display": "none"});
      } else if (el.scrollTop == 0) {
        // console.log("hide top");
        parent.find(".border-shadows-top").css({"display": "none"});
      }
    }
  }

  function pauseClick(e) {
    console.log("pause toggled");
    if (typeof activeTimer !== "undefined") {
      // There is an activeTimer

      if (activeTimer.paused) {
        // Currently is paused. Now that it is clicked, no longer paused.
        activeTimer.unpause();
        $(e).find("#play").css({"display": "none"});
        $(e).find("#pause").css({"display": "block"});
      } else {
        activeTimer.pause();
        $(e).find("#play").css({"display": "block"});
        $(e).find("#pause").css({"display": "none"});
      }
    } else {
      $(e).find("#play").css({"display": "none"});
      $(e).find("#pause").css({"display": "none"});
    }
  }
</script>

<?php
  function render_pill_choices($categoryPills) {
    ?>
    <div class="pill-choices">
      <?php
        foreach($categoryPills as $pill) {
          echo $pill->render();
        }
      ?>
    </div>
    <?php  
  }

  function render_timed_pills($timedPills, $categoryPills) {
    ?>
    <div class="timed-pills-container"> 
      <div class="timed-pills" >
        <div class="scrolling-pills" id="timed_pills_container" onscroll="isOverflown(this)">
          <?php 
            foreach($timedPills as $pill) {
              echo $pill.render();
            }
          ?>

        </div>
        <div class="shadow-container">
          <div class="border-shadows-top"></div>
          <div class="border-shadows-bottom"></div>
        </div>
      </div>
    </div>
    <?php
  }

  function render_pause() {
    ?>
    <div class="pause-button" id="pauseContainer" onclick="pauseClick(this)">  
      <img id="pause" src="../assets/pause.svg" alt="pause button" style="display: none">
      <img id="play" src="../assets/play.svg" alt="play button" style="display: none">
    </div>
    <?php
  }
?>