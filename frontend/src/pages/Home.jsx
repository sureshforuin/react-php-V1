import { useEffect, useState } from 'react';
import { getHealth } from '../services/api.js';

export default function Home() {
  const [health, setHealth] = useState(null);
  const [error, setError]   = useState(null);

  useEffect(() => {
    getHealth()
      .then(setHealth)
      .catch((err) => setError(err.message));
  }, []);

  return (
    <div>
      <div className="card">
        <h2>Welcome 👋</h2>
        <p style={{ marginTop: '0.5rem', color: '#666' }}>
          This is a simple React + PHP REST API app hosted on Hostinger.
        </p>
      </div>

      <div className="card">
        <h2>API Health Status</h2>
        {error && <div className="error-box">❌ {error}</div>}
        {!health && !error && <p className="loading">Checking API...</p>}
        {health && (
          <table className="user-table">
            <tbody>
              <tr><td><strong>Status</strong></td><td>{health.status}</td></tr>
              <tr><td><strong>Server Time</strong></td><td>{health.server_time}</td></tr>
              <tr><td><strong>PHP Version</strong></td><td>{health.php_version}</td></tr>
              <tr><td><strong>DB</strong></td><td>{health.db}</td></tr>
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
