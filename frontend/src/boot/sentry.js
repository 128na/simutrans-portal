import * as Sentry from '@sentry/vue';
import { BrowserTracing } from '@sentry/tracing';

export default ({ app, router }) => {
  console.log('init');
  Sentry.init({
    app,
    dsn: 'https://af4ea06d84a24cec9cb434369da1c4d5@o4504660227719168.ingest.sentry.io/4504683218206720',
    integrations: [
      new BrowserTracing({
        routingInstrumentation: Sentry.vueRouterInstrumentation(router),
        tracePropagationTargets: ['localhost', 'simutrans-portal.128-bit.net', /^\//],
      }),
    ],
    // Set tracesSampleRate to 1.0 to capture 100%
    // of transactions for performance monitoring.
    // We recommend adjusting this value in production
    tracesSampleRate: 1.0,
  });
};
