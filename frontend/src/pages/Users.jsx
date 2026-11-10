import { useEffect, useState } from 'react';
import { getUsers, createUser, deleteUser } from '../services/api.js';

export default function Users() {
  const [users, setUsers]     = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError]     = useState(null);
  const [form, setForm]       = useState({ name: '', email: '' });
  const [saving, setSaving]   = useState(false);

  const fetchUsers = () => {
    setLoading(true);
    getUsers()
      .then(setUsers)
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  };

  useEffect(() => { fetchUsers(); }, []);

  const handleCreate = async (e) => {
    e.preventDefault();
    setSaving(true);
    try {
      await createUser(form);
      setForm({ name: '', email: '' });
      fetchUsers();
    } catch (err) {
      setError(err.message);
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Delete this user?')) return;
    try {
      await deleteUser(id);
      fetchUsers();
    } catch (err) {
      setError(err.message);
    }
  };

  return (
    <div>
      {/* Create User Form */}
      <div className="card">
        <h2>Add User</h2>
        <form onSubmit={handleCreate} style={{ marginTop: '1rem', display: 'flex', gap: '0.75rem', flexWrap: 'wrap' }}>
          <input
            type="text"
            placeholder="Name"
            required
            value={form.name}
            onChange={(e) => setForm({ ...form, name: e.target.value })}
            style={{ padding: '0.5rem 0.75rem', border: '1px solid #ccc', borderRadius: '6px', flex: 1 }}
          />
          <input
            type="email"
            placeholder="Email"
            required
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
            style={{ padding: '0.5rem 0.75rem', border: '1px solid #ccc', borderRadius: '6px', flex: 1 }}
          />
          <button className="btn" type="submit" disabled={saving}>
            {saving ? 'Saving…' : '+ Add User'}
          </button>
        </form>
      </div>

      {/* Users List */}
      <div className="card">
        <h2>All Users</h2>
        {error   && <div className="error-box">❌ {error}</div>}
        {loading && <p className="loading">Loading users…</p>}
        {!loading && !error && (
          <table className="user-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Created</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {users.length === 0 ? (
                <tr><td colSpan={6} style={{ textAlign: 'center', color: '#888' }}>No users yet.</td></tr>
              ) : users.map((u) => (
                <tr key={u.id}>
                  <td>{u.id}</td>
                  <td>{u.name}</td>
                  <td>{u.email}</td>
                  <td>
                    <span className={`badge badge-${u.status}`}>{u.status}</span>
                  </td>
                  <td>{new Date(u.created_at).toLocaleDateString()}</td>
                  <td>
                    <button
                      onClick={() => handleDelete(u.id)}
                      style={{ background: 'none', border: 'none', cursor: 'pointer', color: '#c00', fontSize: '1.1rem' }}
                      title="Delete"
                    >🗑</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
