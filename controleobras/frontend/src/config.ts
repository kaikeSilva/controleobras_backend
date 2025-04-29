// Declare environment variables for TypeScript
declare global {
  interface ImportMetaEnv {
    readonly VITE_API_URL: string;
  }
}

const config = {
  apiBaseUrl: import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
};

export default config;
