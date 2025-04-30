import api from './api';

export interface Obra {
  id?: number;
  nome: string;
  descricao?: string;
  endereco: string;
  data_inicio: string;
  prazo_estimado: number;
  area_m2: number;
  valor_estimado: number;
  taxa_administracao: number;
  created_at?: string;
  updated_at?: string;
}

export interface ObraFilters {
  nome?: string;
  endereco?: string;
  data_inicio_min?: string;
  data_inicio_max?: string;
  prazo_estimado_min?: number;
  prazo_estimado_max?: number;
  valor_estimado_min?: number;
  valor_estimado_max?: number;
  taxa_administracao_min?: number;
  taxa_administracao_max?: number;
  area_m2_min?: number;
  area_m2_max?: number;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
  page?: number;
  per_page?: number;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
}

const obraService = {
  getObras: (filters: ObraFilters = {}): Promise<PaginatedResponse<Obra>> => {
    // Garantir que os parâmetros de paginação sejam números
    const params = {
      ...filters,
      page: Number(filters.page) || 1,
      per_page: Number(filters.per_page) || 10
    };
    
    return api.get('/obras', { params })
      .then((response: any) => response.data);
  },

  getObra: (id: number): Promise<Obra> => {
    return api.get(`/obras/${id}`)
      .then((response: any) => response.data.data);
  },

  createObra: (obra: Obra): Promise<Obra> => {
    return api.post('/obras', obra)
      .then((response: any) => response.data.data);
  },

  updateObra: (id: number, obra: Obra): Promise<Obra> => {
    return api.put(`/obras/${id}`, obra)
      .then((response: any) => response.data.data);
  },

  deleteObra: (id: number): Promise<void> => {
    return api.delete(`/obras/${id}`);
  }
};

export default obraService;
