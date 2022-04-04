document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("form");
    form.addEventListener("submit", formSend);

    async function formSend(e) {
        e.preventDefault();

        let error = formValidate(form);

        const formData = new FormData(form);
        formData.append("image", formImage.files[0]);
        if (error === 0) {
            form.classList.add("_sending");
            let response = await fetch("sendmail.php", {
                method: "POST",
                body: formData,
            });
            console.log(response);
            let resultText = await response.text();
            console.log(resultText);

            if (response.ok) {
                let result = await response.json();
                alert(result.message);
                filePreview.innerHTML = "";
                form.reset();
                form.classList.remove("_sending");
            } else {
                // let result = await response.json();
                // alert(result.message);
                alert("Form was not send. This is the test form with no real data sending. Thank you.");
                form.classList.remove("_sending");
            }
        } else {
            alert("Fill out all requared fields, please.");
        }
    }

    function formValidate(form) {
        let error = 0;
        let formReq = document.querySelectorAll("._req");

        formReq.forEach((input) => {
            formRemoveError(input);

            if (input.classList.contains("_email")) {
                if (emailTest(input)) {
                    formAddError(input);
                    error++;
                }
            } else if (input.getAttribute("type") === "checkbox" && input.checked === false) {
                formAddError(input);
                error++;
            } else {
                if (input.value === "") {
                    formAddError(input);
                    error++;
                }
            }
        });
        return error;
    }

    function formAddError(input) {
        input.parentElement.classList.add("_error");
        input.classList.add("_error");
    }

    function formRemoveError(input) {
        input.parentElement.classList.remove("_error");
        input.classList.remove("_error");
    }

    function emailTest(input) {
        return !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,8})+$/.test(input.value);
    }

    const formImage = document.getElementById("formImage");
    const filePreview = document.getElementById("filePreview");

    formImage.addEventListener("change", () => {
        uploadFile(formImage.files[0]);
    });

    function uploadFile(file) {
        if (!["image/jpeg", "image/png", "image/gif"].includes(file.type)) {
            alert("Only .jpg .png and .gif images allowed");
            formImage.value = "";
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert("Image should be < 2Mb");
            return;
        }

        let reader = new FileReader();
        reader.onload = function (e) {
            filePreview.innerHTML = `<img src="${e.target.result}" alt="screnshot">`;
        };
        reader.onerror = function (e) {
            alert("something went wrong... Try again");
        };
        reader.readAsDataURL(file);
    }
});
