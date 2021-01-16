<?php 
  function insertCategoryListScripts() {
    ?>
  <script>
    function saveSession(sessionId) {
      activeTimer.updateTimePill(
        () => {
          let request = $.ajax({
            url: "/requests/save_session.php",
            type: "post",
            data: {
              saveSession: {
                sessionId: sessionId
              }
            }
          });

          request.done(function (isDraft, textStatus, jqXHR){
            if (parseInt(isDraft)) {
              $("#save").html("Save");
            } else {
              $("#save").html("Unsave")
            }
          });
        }
      )
      
    }

    function isOverflown(el) {
      // No pixels to scroll at bottom
      var parent = $(el).parent();
      if (el.scrollHeight <= el.clientHeight) {
        parent.find(".border-shadows-bottom").css({"display": "none"});
        parent.find(".border-shadows-top").css({"display": "none"});
      } else  {
        parent.find(".border-shadows-bottom").css({"display": "block"});
        parent.find(".border-shadows-top").css({"display": "block"});
        
        if (Math.abs(el.scrollHeight - el.clientHeight - el.scrollTop) < 3) {
          // console.log("hide bottom");
          parent.find(".border-shadows-bottom").css({"display": "none"});
        } else if (el.scrollTop == 0) {
          // console.log("hide top");
          parent.find(".border-shadows-top").css({"display": "none"});
        }
      }
    }
  </script>
<?php
  }

  function render_save($session) {
    $text = $session["draft"] ? "Save" : "Unsave";
    ?> 
    <div id="save" onclick="saveSession(<?= $session['id'] ?>, <?= $session['draft'] ?>)" class="save"><?= $text ?></div> 
    <?php
  }

  function render_top_bar($session) {
    ?>
    <div class="top-bar">
      <a href="/" class="back"><img src="../assets/back.svg" alt="pause button"></a>
      <div class="title">ACTIVE SESSION</div>
      <?= render_save($session); ?>
    </div>
    <?php
  }


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
              echo $pill->render();
            }
            initiateTimer();
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
    <div class="pause-button" id="pauseContainer" onclick="activeTimer.togglePause()">  
      <img id="pause" src="../assets/pause.svg" alt="pause button" style="display: none">
      <img id="play" src="../assets/play.svg" alt="play button" style="display: none">
    </div>
    <script>
      activeTimer.togglePause();
    </script>
    <?php
  }
?>