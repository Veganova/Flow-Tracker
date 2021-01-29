<?php
  function insertCategoryPillScripts() {
  ?>
  <script>
    // TODO figure out how to save on page close
    window.addEventListener('onunload', function (e) {
        e.preventDefault();
        e.returnValue = '';

        console.log("go for the updated!!");
        activityTimer.updateTimePill(()=>{});
    });

    function addTimedPill(pillId) {
      console.log("adding timed pill", pillId);
      activeTimer.updateTimePill(() => {
        let container = document.getElementById("timed_pills_container");

      const data = { 
        addPill: {
          categoryId: pillId,
          sessionId: new URLSearchParams(window.location.search).get("session"),
        }
      }
      let request = $.ajax({
        url: "/requests/pill.php",
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
      });
    }

    function editCategory(id, name, color, pillId) {
      let request = $.ajax({
        url: "/requests/category.php",
        type: "post",
        data: {
          editCategory: { 
            id: id,
            name: name, 
            color: color
          }
        }
      });

      request.done(function (response, textStatus, jqXHR) {
        $(`#${pillId}`).replaceWith(response);
      });

      request.fail(function (jqXHR, textStatus, errorThrown){
        console.error("The following error occurred: ", textStatus, errorThrown);
      });
    }

    function removeCategoryFunc(id, pillID, dropdownId) {
      return () => {
        let dropdownInstance = document.getElementById(dropdownId).instance;
        dropdownInstance && dropdownInstance.hide();

        let request = $.ajax({
          url: "/requests/category.php",
          type: "post",
          data: {
            removeCategory: { 
              id: id
            }
          }
        });

        request.done(function (response, textStatus, jqXHR) {
          if (response) {
            $(`#${pillID}`).remove()
          }
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
          console.error("The following error occurred: ", textStatus, errorThrown);
        });
      }
    }

    function addNewCategory(name, color) {
      let request = $.ajax({
        url: "/requests/category.php",
        type: "post",
        data: {
          addCategory: { 
            name: name, 
            color: color
          }
        }
      });

      request.done(function (response, textStatus, jqXHR){
        // console.log("added category", response);
        $(".pill-choices").append(response);
      });

      request.fail(function (jqXHR, textStatus, errorThrown){
        console.error("The following error occurred: ", textStatus, errorThrown);
      });
    }

    function addToolTip(dropdownId, tooltipId) {
      let tooltipContent = document.getElementById(tooltipId).innerHTML;
      tippy(`#${dropdownId}`, {
        zIndex: 1,
        content: tooltipContent,
        allowHTML: true,
        trigger: "click manual",
        interactive: true,
        onShown: function(instance) {
          // Save so the tippy popup can be hidden with instance.hide()
          document.getElementById(dropdownId).instance =  instance;
        },
        appendTo: () => document.body,
        theme: 'light'
      });
    }

    function toggleTooltip(e) {
      e.preventDefault();
      e.stopPropagation();
    }
  </script>
  <?php
}

  require_once "iDrawable.php";

  // Once clicked will add timed pills to the page
  class CategoryPill implements iDrawable {
    public function __construct($id, $name, $color, $active=true) {
      $this->id = htmlspecialchars($id);
      $this->name = htmlspecialchars($name);
      $this->color = htmlspecialchars($color);
      $this->active = htmlspecialchars($active);
    }

    private function getDropdownId() {
      return "dropdown-".$this->id;
    }

    private function getDOMId() {
      return "pill-".$this->id;
    }

    private function getTooltipId() {
      return "tooltip-".$this->id;
    }

    private function renderContent() {
      ?>
        <input class="plain-input" readonly value="<?= $this->name; ?>">
      <?php
    }

    // $realpage -> not a demo render
    public function renderPill($dropdown=false) {
      global $ROOT;
      ?>
        <div 
          id="<?= $this->getDOMId(); ?>"
          value="<?=$this->name?>"
          class="pill"
          style="background: <?= $this->color; ?>"
          onclick="addTimedPill(<?= $this->id ?>)"
        >
          <?= $this->renderContent(); ?>
          <?php if($dropdown) { ?>
            <div id="<?= $this->getDropdownId() ?>" onclick="toggleTooltip(event)" alt="dropdown button" class="edit-pill-dropdown">
              <?= file_get_contents($ROOT."assets/dropdown.svg"); ?>
            </div>
          <?php } ?>
        </div>
      <?php
    }

    public function renderTooltip() {
      ?>
      <div id="<?= $this->getTooltipId() ?>"  style="display: none;">
        <div class="category-tooltip">
          <div 
          onclick='editCategoryModal(
            "<?= $this->id ?>", 
            "<?= $this->getDOMId() ?>", 
            "<?= $this->getDropdownId() ?>",
            "<?= $this->name ?>", 
            "<?= $this->color ?>")'>
            Edit
          </div>
        <div onclick="confirmActionModal('<?= $this->getDropdownId() ?>', removeCategory('<?= $this->id ?>', '<?= $this->getDOMId() ?>', '<?= $this->getDropdownId() ?>'))">
            Remove
          </div>
        </div>
      </div>  
        <script>
          addToolTip('<?= $this->getDropdownId();?>', '<?= $this->getTooltipId(); ?>');
        </script>
      <?php
    }
    public function render() {
      ?>
      <div class="single-pill-container">
        <?= $this->renderPill(true); ?>
        <?= $this->renderTooltip(); ?>
      </div>
      <?php
    }
  }
?>