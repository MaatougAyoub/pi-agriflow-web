import { startStimulusApp } from '@symfony/stimulus-bundle';
import AiAssistantController from './controllers/ai_assistant_controller.js';

const app = startStimulusApp();
app.register('ai-assistant', AiAssistantController);
