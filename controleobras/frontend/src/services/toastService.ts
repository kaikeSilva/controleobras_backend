import { ref, reactive } from 'vue';

export type ToastType = 'success' | 'error' | 'warning' | 'info';

export interface ToastState {
  show: boolean;
  type: ToastType;
  title: string;
  message: string;
  duration: number;
}

// Estado inicial do toast
const toastState = reactive<ToastState>({
  show: false,
  type: 'info',
  title: '',
  message: '',
  duration: 3000
});

// Função para mostrar toast
export function showToast(options: {
  type?: ToastType;
  title?: string;
  message: string;
  duration?: number;
}) {
  // Esconder qualquer toast existente
  toastState.show = false;
  
  // Pequeno timeout para garantir que a transição de saída seja concluída
  setTimeout(() => {
    toastState.type = options.type || 'info';
    toastState.title = options.title || getTitleByType(options.type || 'info');
    toastState.message = options.message;
    toastState.duration = options.duration || 3000;
    toastState.show = true;
  }, 100);
}

// Função para esconder toast
export function hideToast() {
  toastState.show = false;
}

// Funções de conveniência para diferentes tipos de toast
export function showSuccessToast(message: string, title?: string, duration?: number) {
  showToast({
    type: 'success',
    title: title || 'Sucesso',
    message,
    duration
  });
}

export function showErrorToast(message: string, title?: string, duration?: number) {
  showToast({
    type: 'error',
    title: title || 'Erro',
    message,
    duration
  });
}

export function showWarningToast(message: string, title?: string, duration?: number) {
  showToast({
    type: 'warning',
    title: title || 'Atenção',
    message,
    duration
  });
}

export function showInfoToast(message: string, title?: string, duration?: number) {
  showToast({
    type: 'info',
    title: title || 'Informação',
    message,
    duration
  });
}

// Função auxiliar para obter título padrão com base no tipo
function getTitleByType(type: ToastType): string {
  switch (type) {
    case 'success':
      return 'Sucesso';
    case 'error':
      return 'Erro';
    case 'warning':
      return 'Atenção';
    case 'info':
      return 'Informação';
    default:
      return 'Notificação';
  }
}

// Hook para usar o toast em componentes Vue
export function useToast() {
  return {
    toast: {
      success: (message: string, title?: string, duration?: number) => showSuccessToast(message, title, duration),
      error: (message: string, title?: string, duration?: number) => showErrorToast(message, title, duration),
      warning: (message: string, title?: string, duration?: number) => showWarningToast(message, title, duration),
      info: (message: string, title?: string, duration?: number) => showInfoToast(message, title, duration)
    }
  };
}

export default {
  toastState,
  showToast,
  hideToast,
  showSuccessToast,
  showErrorToast,
  showWarningToast,
  showInfoToast,
  useToast
};
