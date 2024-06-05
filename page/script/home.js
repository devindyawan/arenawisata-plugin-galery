const xhr = new XMLHttpRequest();

// pagination

// All initial
const url = `${document.location.origin}/arenawisata/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=getall`;
fetch(url)
  .then((response) => response.json())
  .then((body) => {
    const pagination = document.getElementById("all");
    const page = Math.ceil(body.page / 21);

    if (pagination) {
      for (let index = 0; index < page; index++) {
        if (index === 0) {
          const prev = document.createElement("button");
          const angka = document.createElement("button");

          prev.innerText = "Previous";
          angka.innerText = index + 1;
          angka.setAttribute("key", index);
          angka.setAttribute("id", "alldata");

          pagination.append(prev, angka);
        } else if (index > 0) {
          const angka = document.createElement("button");
          angka.innerText = index + 1;
          angka.setAttribute("key", index * 21);
          angka.setAttribute("id", "alldata");

          pagination.append(angka);
        }
      }
    }
  });

const previewImage = (event) => {
  let image = document.getElementById("imagegalery");

  const previewContainer = document.getElementById("preview");
  previewContainer.innerHTML = "";

  for (let index = 0; index < image.files.length; index++) {
    let img = document.createElement("img");
    img.src = URL.createObjectURL(image.files[index]);
    img.classList.add("item");

    previewContainer.append(img);
  }
};

const form = document.querySelector("form");

if (form) {
  form.addEventListener("submit", (event) => {
    const formData = new FormData(form);
    const url = `${document.location.origin}/arenawisata/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=form`;

    // Display status
    const homeContainer = document.querySelector(".home");
    const statusUpload = document.createElement("div");
    const statustext = document.createElement("h2");

    statustext.innerText = "Uploading ...";
    statusUpload.append(statustext);

    statusUpload.classList.add("statusupload");

    homeContainer.append(statusUpload);

    const fetchOption = {
      method: form.method,
      body: formData,
    };

    fetch(url, fetchOption)
      .then((response) => response.json())
      .then((body) => {
        if (body.status === 201) {
          statustext.innerText = "Image Uploaded";
          setTimeout(() => {
            const previewContainer = document.getElementById("preview");
            const catvalue = document.getElementById("category");
            catvalue.value = "default";
            previewContainer.innerHTML = "";
            statusUpload.remove();
          }, 2000);
        } else if (body.status === 400) {
          statustext.innerText = "Bad Request";
          setTimeout(() => {
            statusUpload.remove();
          }, 2000);
        }
      });

    // console.log(fetchOption);

    event.preventDefault();
  });
}

const windowclick = (event) => {
  const galerymodal = document.getElementById("galerymodal");
  const title = document.getElementById("title");
  const detail = document.querySelector(".detail");
  const description = document.getElementById("description");
  const notif = document.getElementById("notification");

  // Pagination
  if (event.target.closest("#all") && event.target.tagName === "BUTTON") {
    const page = event.target.getAttribute("key");

    const url = `${document.location.origin}/arenawisata/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=getall&page=${page}`;

    const button = event.target.closest("#all").children;
    for (let index = 0; index < button.length; index++) {
      const element = button[index];

      element.style.backgroundColor = "#fcfcfc";
    }

    event.target.style.backgroundColor = "grey";

    fetch(url)
      .then((response) => response.json())
      .then((body) => {
        const galery = document.querySelector(".galeryimage");

        galery.innerHTML = "";
        body.data.forEach((element) => {
          const img = document.createElement("img");
          const div = document.createElement("div");

          //PERLU DIUBAH HILANGKAN ARENAWISATA
          div.classList.add("imagelist");
          img.src = `${document.location.origin}/arenawisata/${element.filelocation}`;
          img.alt = element.title;
          img.setAttribute("key", element.id);
          img.setAttribute("id", "image");

          div.append(img);
          galery.append(div);
        });
      });
  }

  // Update button
  if (event.target.closest(".update") && event.target.tagName === "BUTTON") {
    const newtitle = document.getElementById("titleinput").value;
    const newdesc = description.value;
    const newcategory = document.getElementById("categoryinput").value;
    const thisid = document.querySelector(".thisid").getAttribute("key");

    // console.log(thisid.getAttribute("key"));

    const url = `${document.location.origin}/arenawisata/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=updategalery&id=${thisid}`;

    const data = {
      title: newtitle,
      description: newdesc,
      id_category: newcategory,
    };

    xhr.onloadend = () => {
      if (xhr.responseText == 1) {
        notif.style.opacity = 1;
      }
    };

    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify(data));
  } else if (
    event.target.closest("#galery") &&
    event.target.tagName === "IMG"
  ) {
    const image = event.target;
    const galeryId = image.getAttribute("key");

    // Hapus arenawisata jika sudah deploy
    const url = `${document.location.origin}/arenawisata/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=galery&id=${galeryId}`;

    xhr.onloadend = () => {
      const respons = JSON.parse(xhr.responseText);
      const imageview = document.querySelector(".imageview");
      const titleInput = document.getElementById("titleinput");
      const categoryInput = document.getElementById("categoryinput");
      const img = document.createElement("img");

      const listheader = [
        "ID",
        "Kategory",
        "Diupload oleh",
        "Nama File",
        "Tanggal Upload",
      ];

      const listbody = [
        respons[0].id,
        respons[0].category,
        respons[0].uploadby,
        respons[0].filename,
        respons[0].date,
      ];

      const table = document.createElement("table");
      table.classList.add("tabledetail");

      for (let index = 0; index < listheader.length - 1; index++) {
        const tr = document.createElement("tr");

        const tdhead = document.createElement("td");
        const tdbody = document.createElement("td");

        tdhead.innerText = listheader[index];
        tdbody.innerText = listbody[index];

        tr.append(tdhead, tdbody);
        table.append(tr);
      }

      detail.append(table);

      const id =
        document.getElementsByClassName("tabledetail")[0].children[0]
          .children[1];

      id.classList.add("thisid");
      id.setAttribute("key", respons[0].id);

      title.innerText = respons[0].title;
      titleInput.value = respons[0].title;
      categoryInput.value = respons[0].id_category;
      description.value = respons[0].description;
      // filelocation.innerText = `File Location : ${respons[0].filelocation}`;

      img.src = `${document.location.origin}/arenawisata/${respons[0].filelocation}`;
      img.classList.add("image");

      imageview.append(img);
      galerymodal.style.display = "block";
    };

    xhr.open("GET", url, true);
    xhr.send();
  } else if (
    !event.target.closest("#galerymodal") ||
    event.target.closest("#close")
  ) {
    const imageview = document.querySelector(".imageview");
    if (imageview) {
      imageview.innerHTML = "";
      detail.innerHTML = "";
      description.value = "";
      galerymodal.style.animation = "fadeOut 0.3s";
      notif.style.opacity = 0;
      setTimeout(() => {
        galerymodal.style.display = "none";
        galerymodal.style.animation = "fadeIn 0.3s";
      }, 200);
    }
  }
};

window.addEventListener("click", windowclick);

const getByCategory = (event) => {
  const category = document.getElementById("category");
  const galery = document.querySelector(".galeryimage");

  // const url = `${document.location.origin}/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=category&cat=${category.value}`;

  //PERLU DIUBAH HILANGKAN ARENAWISATA
  const url = `${document.location.origin}/arenawisata/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=category&cat=${category.value}`;

  // console.log(url);

  xhr.onloadend = () => {
    const result = JSON.parse(xhr.responseText);

    galery.innerHTML = "";
    result.forEach((element) => {
      const img = document.createElement("img");
      const div = document.createElement("div");

      //PERLU DIUBAH HILANGKAN ARENAWISATA
      div.classList.add("imagelist");
      img.src = `${document.location.origin}/arenawisata/${element.filelocation}`;
      img.alt = element.title;
      img.setAttribute("key", element.id);
      img.setAttribute("id", "image");

      div.append(img);
      galery.append(div);
    });
  };

  xhr.open("GET", url, true);
  xhr.send();
};

const getByDate = (event) => {
  const dateValue = document.getElementById("date");
  const galery = document.querySelector(".galeryimage");

  // const url = `${document.location.origin}/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=category&cat=${category.value}`;

  //PERLU DIUBAH HILANGKAN ARENAWISATA
  const url = `${document.location.origin}/arenawisata/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=date&date=${dateValue.value}`;

  xhr.onloadend = () => {
    const result = JSON.parse(xhr.responseText);

    // galery.innerHTML = result;

    galery.innerHTML = "";
    result.forEach((element) => {
      const img = document.createElement("img");
      const div = document.createElement("div");

      //PERLU DIUBAH HILANGKAN ARENAWISATA
      div.classList.add("imagelist");
      img.src = `${document.location.origin}/arenawisata/${element.filelocation}`;
      img.alt = element.title;
      img.setAttribute("key", element.id);
      img.setAttribute("id", "image");

      div.append(img);
      galery.append(div);
    });
  };

  xhr.open("GET", url, true);
  xhr.send();
};

let deleteMultiple = [];
let cond = false;

const selection = (event) => {
  const imagecontainer = event.target.closest(".imagelist");

  if (imagecontainer) {
    if (imagecontainer.style.backgroundColor !== "rgb(113, 179, 255)") {
      imagecontainer.style.backgroundColor = "rgb(113, 179, 255)";
      deleteMultiple.push(event.target.getAttribute("key"));
    } else {
      imagecontainer.style.backgroundColor = "transparent";
      let value = event.target.getAttribute("key");

      deleteMultiple = deleteMultiple.filter((item) => item !== value);
    }
  }

  if (event.target.closest("#delete")) {
    if (deleteMultiple.length !== 0) {
      const url = `${document.location.origin}/arenawisata/wp-content/plugins/arenawisata-galery/page/controller/controller.php?fun=delete`;
      // const data = {
      //   images: deleteMultiple.toString(),
      // };

      fetch(url, {
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        method: "POST",
        body: JSON.stringify(deleteMultiple.toString()),
      })
        .then((response) => response.json())
        .then((body) => {
          if (body.status === 200) {
            const imagelist = document.querySelectorAll(".imagelist img");
            const imageItem = document.querySelectorAll(".imagelist");

            deleteMultiple.filter((item) => {
              for (let index = 0; index < imagelist.length; index++) {
                if (item === imagelist[index].getAttribute("key")) {
                  imageItem[index].remove();
                }
              }
            });

            window.removeEventListener("click", selection);
            window.addEventListener("click", windowclick);
            const image = document.querySelectorAll("#image");
            const divdelete = document.querySelector(".delbutton");

            divdelete.innerHTML = "";

            for (let index = 0; index < image.length; index++) {
              const element = image[index];
              element.style.height = "200px";
              element.style.width = "200px";
              element.closest(".imagelist").style.backgroundColor =
                "transparent";
            }
            cond = false;
          }
        });
    }
  }

  event.preventDefault();
};

const handleSelect = () => {
  const delbutton = document.createElement("button");
  const divdelete = document.querySelector(".delbutton");
  const image = document.querySelectorAll("#image");

  if (!cond) {
    window.removeEventListener("click", windowclick);
    window.addEventListener("click", selection);

    delbutton.innerText = "Delete";
    delbutton.setAttribute("id", "delete");
    divdelete.append(delbutton);

    for (let index = 0; index < image.length; index++) {
      const element = image[index];
      element.style.height = "190px";
      element.style.width = "190px";
    }
    cond = true;
  } else if (cond) {
    window.addEventListener("click", windowclick);
    window.removeEventListener("click", selection);

    divdelete.innerHTML = "";
    deleteMultiple = [];

    for (let index = 0; index < image.length; index++) {
      const element = image[index];
      element.style.height = "200px";
      element.style.width = "200px";
      element.closest(".imagelist").style.backgroundColor = "transparent";
    }
    cond = false;
  }
};
