import "./bootstrap";
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';
import lottie from 'lottie-web';
import.meta.glob(["../images/**"]);

window.livewire = Livewire;
window.lottie = lottie;

Livewire.start()
