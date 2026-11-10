const BASE_URL = import.meta.env.VITE_API_URL;

/**
 * Generic fetch wrapper with error handling.
 */
async function request(endpoint, options = {}) {
  const response = await fetch(`${BASE_URL}${endpoint}`, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  });

  if (!response.ok) {
    const error = await response.json().catch(() => ({ message: 'Unknown error' }));
    throw new Error(error.message || `HTTP ${response.status}`);
  }

  return response.json();
}

// ── Users ──────────────────────────────────────────────
export const getUsers  = ()        => request('/users');
export const getUser   = (id)      => request(`/users/${id}`);
export const createUser = (data)   => request('/users', { method: 'POST', body: JSON.stringify(data) });
export const updateUser = (id, data) => request(`/users/${id}`, { method: 'PUT', body: JSON.stringify(data) });
export const deleteUser = (id)     => request(`/users/${id}`, { method: 'DELETE' });

// ── Health ─────────────────────────────────────────────
export const getHealth = () => request('/health');
