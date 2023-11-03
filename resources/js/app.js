import "./bootstrap";
import Alpine from "alpinejs";
import lottie from 'lottie-web';
import.meta.glob(["../images/**"]);

window.Alpine = Alpine;
window.lottie = lottie;

Alpine.start();
