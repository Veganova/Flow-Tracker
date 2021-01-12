<script>
  function addTimedPill(pillId) {
    var container = document.getElementById("timed_pills_container");

    var request = $.ajax({
      url: "/requests/add_pill.php",
      type: "post",
      data: {addPill: pillId}
    })

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR){
        // Log a message to the console
        container.innerHTML += response;
        container.dispatchEvent(new CustomEvent('scroll'));
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