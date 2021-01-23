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

  function editCategoryModal(id, domID, dropdownId, name, color, htmlString="") {
      let dropdownInstance = document.getElementById(dropdownId).instance;
      dropdownInstance && dropdownInstance.hide();

      console.log("edit div", document.getElementById(domID));
      let div = document.getElementById(domID).cloneNode(true);
      div.removeAttribute("onclick");
      div.setAttribute("id", "temp-pill-edit");

      openModal(
        `
        <div id="edit-modal" class="color-picker-modal">
          <div class="color-picker-row"> 
            ${div.outerHTML}
            <input type="color" id="edit-color-picker" value="${color}">
          </div>
          <div class="confirm-button">Edit</div>
        </div>
        `
      );

      let modal = $(document.getElementById("edit-modal"));

      let tempPill = modal.find("#temp-pill-edit").get(0);
      let onColorChange = function(e) {
        color = e.target.value;
        tempPill.setAttribute("style", `background: ${color};`);
      };

      let colorPicker = modal.find("#edit-color-picker").get(0);
      colorPicker.addEventListener("input", onColorChange, false);
      colorPicker.addEventListener("change", onColorChange, false);

      modal.find(".plain-input").removeAttr("readonly");
      modal.find(".plain-input").focus();

      let confirm = modal.find(".confirm-button").get(0);
      confirm.addEventListener("click", () => {
        name = modal.find(".plain-input").get(0).value;
        editCategory(id, name, color, domID);
        closeModal();
      });
    }

  function colorPickerModal() {
      let name = $("#search").get(0).value;
      let color = "#444444";

      let htmlString = `
      <?php
        $tempPill = new CategoryPill(-1, "", "#444444");
        echo $tempPill->renderPill();
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

