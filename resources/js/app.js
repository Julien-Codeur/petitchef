import './bootstrap';
import { OrderPoller, Toaster } from './polling';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.OrderPoller = OrderPoller;
window.Toaster = Toaster;

Alpine.start();
