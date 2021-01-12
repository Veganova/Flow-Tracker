<?php
  require_once "iDrawable.php";

  class CategoryTimedPill {
    private function renderContent() {
      return $this->name." 0:00";
    }

    public function render() {
      ?>
        <div 
          class="pill-timed"
          style="background: <?= $this->color; ?>"
          onclick="alert('showsomestuff');"
        >
          <?= $this->renderContent(); ?>
        </div>
      <?php
    }
  }
?>