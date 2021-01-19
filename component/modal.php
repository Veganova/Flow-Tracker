<?php
  function insertModalScripts() {
?>

  <div id="myModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <div class="new-content"></div>
    </div>
  </div>

  <script>
  let modal = document.getElementById("myModal");

  function openModal(content) {
    modal.style.display = "flex";

    $(modal).find(".new-content").html(content);
  }

  // When the user clicks on <span> (x), close the modal
  function closeModal() {
    modal.style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    document.getElementById("myModal");
    if (event.target == modal) {
      closeModal();
    }
  }

  function colorPickerModal() {
      let name = $("#search").get(0).value;
      let color = "#444444";

      let htmlString = `
      <?php
        $tempPill = new CategoryPill(-1, "", "#444444");
        echo $tempPill->render();
      ?>
      `;

      var div = document.createElement('div');
      div.innerHTML = htmlString.trim();
      div.firstChild.removeAttribute("onclick");
      div.firstChild.setAttribute("id", "temp-pill");
      div.firstChild.innerHTML = name;

      console.log("div", name, div);

      openModal(
        `
        <div class="color-picker-modal">
          <div class="color-picker-row"> 
            ${div.innerHTML}
            <input type="color" id="color-picker" value="${color}">
          </div>
          <div class="confirm-button">Confirm</div>
        </div>
        `
      );

      let tempPill = document.getElementById("temp-pill");
      let onColorChange = function(e) {
        color = e.target.value;
        tempPill.setAttribute("style", `background: ${color};`);
      };

      let colorPicker = document.getElementById("color-picker");
      colorPicker.addEventListener("input", onColorChange, false);
      colorPicker.addEventListener("change", onColorChange, false);

      let confirm = document.querySelector(".confirm-button");
      confirm.addEventListener("click", () => {
        addNewCategory(name, color);
        closeModal();
      });
    }
  </script>
<?php
  }
?>

