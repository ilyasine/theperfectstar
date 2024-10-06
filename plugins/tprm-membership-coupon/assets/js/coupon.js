

// make copy functionnality  accessible even if pdf is not generated or the there is an error in js file

const copyBtn = document.querySelector(".coupon-container .cpnbtn");
const codetxt = document.querySelector(".coupon-container .code");
copyBtn.addEventListener("click", () => {
    navigator.clipboard.writeText(codetxt.innerHTML);
    copyBtn.innerHTML = kmc_translate.copied;
    setTimeout(() => {
        copyBtn.innerHTML = kmc_translate.cp_code;
    }, 5000);
});


const couponContainer = document.querySelector(".coupon-container");
const couponbox = document.getElementById("coupon-container-empty");

if (couponbox) {

    const printcouponbox = couponbox.querySelector(".coupon-container-inner");

    const savecouponbtn = document.querySelector(".coupon-container a.savecouponbtn");


    savecouponbtn.addEventListener("click", () => {
        setTimeout(() => {
            appendElement();
            generateAndRemove();
        }, 2000);
    });

    window.jsPDF = window.jspdf.jsPDF;
    function generatePdf() {

        html2canvas(printcouponbox, { allowTaint: true }).then(canvas => {
            const pdf = new jsPDF();
            const imgData = canvas.toDataURL('image/png');
            let imgx = 28;
            pdf.addImage(imgData, 'PNG', imgx, 25, 155, 200);
            pdf.setTextColor(255, 255, 255);
            pdf.setFont('Roboto-Black');
            pdf.setFontSize(38);
            pdf.text(imgx + 30, 111, kmc_coupon);
            pdf.save('tepunareomaori-coupon-' + kmc_coupon + '.pdf');
        });

    }


    async function generateAndRemove() {
        // Wait for the generatePdf() function to complete
        await generatePdf();

        // Remove the DOM element
        if (printcouponbox && printcouponbox.parentNode) {
            printcouponbox.parentNode.removeChild(printcouponbox);
        }
    }

    generateAndRemove();


    function appendElement() {
        // Append the removed element (printcouponbox) back to its original parent node
        if (couponContainer) {
            couponContainer.appendChild(printcouponbox);
        }
    }

}





