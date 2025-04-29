import api from './api';

export interface CategoriaGasto {
  id?: number;
  nome: string;
  descricao?: string;
  obras?: number[];
  created_at?: string;
  updated_at?: string;
}

export interface CategoriaGastoFilters {
  nome?: string;
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

const categoriaGastoService = {
  getCategorias: (filters: CategoriaGastoFilters = {}): Promise<PaginatedResponse<CategoriaGasto>> => {
    return api.get('/categorias-gasto', { params: filters })
      .then(response => response.data);
  },

  getCategoria: (id: number): Promise<CategoriaGasto> => {
    return api.get(`/categorias-gasto/${id}`)
      .then(response => response.data.data);
  },

  createCategoria: (categoriaGasto: CategoriaGasto): Promise<CategoriaGasto> => {
    return api.post('/categorias-gasto', categoriaGasto)
      .then(response => response.data.data);
  },

  updateCategoria: (id: number, categoriaGasto: CategoriaGasto): Promise<CategoriaGasto> => {
    return api.put(`/categorias-gasto/${id}`, categoriaGasto)
      .then(response => response.data.data);
  },

  deleteCategoria: (id: number): Promise<void> => {
    return api.delete(`/categorias-gasto/${id}`);
  }
};

export default categoriaGastoService;
