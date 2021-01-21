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

    function addToolTip(pillId, tooltipId) {
      const pill = document.getElementById(pillId);
      const tooltip = document.getElementById(tooltipId);
      Popper.createPopper(pill, tooltip, {
        placement: 'top-end',
        modifiers: [
          {
            name: 'offset',
            options: {
              padding: 5, // 5px from the edges of the popper
              offset: [15, 5],
            },
          },
        ],
      });
    }

    function toggleTooltip(tooltipId) {
      const tooltip = document.getElementById(tooltipId);
      console.log("data-show", tooltip.getAttribute('data-show'));

      if (tooltip.getAttribute('data-show')) {
        tooltip.removeAttribute('data-show');
      } else {
        tooltip.setAttribute('data-show', '');
      }
    }
  </script>
  <?php
}

  require_once "iDrawable.php";

  // Once clicked will add timed pills to the page
  class CategoryPill implements iDrawable {
    public function __construct($id, $name, $color) {
      $this->id = $id;
      $this->name = $name;
      $this->color = $color;
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
      return $this->name;
    }

    public function renderPill($dropdown=false) {
      ?>
        <div 
          id="<?= $this->getDOMId(); ?>"
          value="<?=$this->name?>"
          class="pill"
          style="background: <?= $this->color; ?>"
          onclick="addTimedPill(<?= $this->id ?>)"
        >
          <div><?= $this->renderContent(); ?></div>
          <?php if($dropdown) { ?>
            <div id="<?= $this->getDropdownId() ?>" alt="dropdown button" class="edit-pill-dropdown" onclick="showDropDown()">
              <?= file_get_contents("assets/dropdown.svg"); ?>
            </div>
          <?php } ?>
        </div>
      <?php
    }

    public function renderTooltip() {
      ?>
        <!-- <div class="category-tooltip" id="<?= $this->getTooltipId() ?>" role="tooltip">
          <div>Edit</div>
          <div>Remove</div>

          <div class="tooltip-arrow" data-popper-arrow></div>
        </div> -->
        <script>
          tippy('#<?= $this->getDropdownId() ?>', {
            content: 'My tooltip!',
          });
          // addToolTip('<?= $this->getDOMId();?>', '<?= $this->getTooltipId(); ?>');
        </script>
      <?php
    }


    public function render() {
      ?>
      <div class="single-pill-container">
        <?= $this->renderPill(true); ?>
        <!-- <?= $this->renderTooltip(); ?> -->
      </div>
      <?php
    }
  }
?>