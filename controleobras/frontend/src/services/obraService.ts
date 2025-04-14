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
    return api.get('/obras', { params: filters })
      .then(response => response.data);
  },

  getObra: (id: number): Promise<Obra> => {
    return api.get(`/obras/${id}`)
      .then(response => response.data.data);
  },

  createObra: (obra: Obra): Promise<Obra> => {
    return api.post('/obras', obra)
      .then(response => response.data.data);
  },

  updateObra: (id: number, obra: Obra): Promise<Obra> => {
    return api.put(`/obras/${id}`, obra)
      .then(response => response.data.data);
  },

  deleteObra: (id: number): Promise<void> => {
    return api.delete(`/obras/${id}`);
  }
};

export default obraService;
