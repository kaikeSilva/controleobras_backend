interface Config {
  apiBaseUrl: string;
  appTitle: string;
}

const config: Config = {
  apiBaseUrl: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8081/api',
  appTitle: import.meta.env.VITE_APP_TITLE || 'Laravel Vue App',
};

export default config;
