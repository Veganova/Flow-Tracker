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
</script>

<?php
  function render_pill_choices() {
    global $pdo;

    $stmt = $pdo->query('SELECT * FROM category');
    $pills = $stmt->fetchAll(PDO::FETCH_CLASS, "CategoryPill");

    ?>
    <div class="pill-choices">
      <?php
        foreach($pills as $pill) {
          echo $pill->render();
        }
      ?>
    </div>
    <?php  
  }

  function render_timed_pills($session_id) {
    ?>
    <div class="timed-pills-container"> 
      <div class="timed-pills" >
        <div class="scrolling-pills" id="timed_pills_container" onscroll="isOverflown(this)">

        </div>
        <div class="shadow-container">
          <div class="border-shadows-top"></div>
          <div class="border-shadows-bottom"></div>
        </div>
      </div>
    </div>
    <?php
  }
?>