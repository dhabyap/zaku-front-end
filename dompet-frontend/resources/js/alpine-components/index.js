// resources/js/alpine-components/index.js
// Entry point: registers every Alpine component group.

import { registerAuth } from './auth';
import { registerDashboard } from './dashboard';
import { registerTransactions } from './transactions';
import { registerChat } from './chat';
import { registerBudgets } from './budgets';
import { registerOnboarding } from './onboarding';
import { registerRecap } from './recap';

export default function registerComponents(Alpine) {
    registerAuth(Alpine);
    registerDashboard(Alpine);
    registerTransactions(Alpine);
    registerChat(Alpine);
    registerBudgets(Alpine);
    registerOnboarding(Alpine);
    registerRecap(Alpine);
}
