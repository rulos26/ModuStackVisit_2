document.addEventListener("DOMContentLoaded", function () {
    const steps = document.querySelectorAll(".step-horizontal");
    let currentStep = 1;

    function updateSteps() {
        steps.forEach((step, index) => {
            if (index < currentStep - 1) {
                step.classList.add("complete");
                step.classList.remove("active");
            } else if (index === currentStep - 1) {
                step.classList.add("active");
                step.classList.remove("complete");
            } else {
                step.classList.remove("active", "complete");
            }
        });
    }

    document.getElementById("prevBtn").addEventListener("click", function () {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    });

    document.getElementById("nextBtn").addEventListener("click", function () {
        if (currentStep < steps.length) {
            currentStep++;
            updateSteps();
        }
    });

    updateSteps();
}); 