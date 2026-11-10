import { Routes, Route, Link } from 'react-router-dom';
import Home from './pages/Home.jsx';
import Users from './pages/Users.jsx';
import './App.css';

function App() {
  return (
    <div className="app">
      <nav className="navbar">
        <span className="brand">⚛️ React + PHP App</span>
        <div className="nav-links">
          <Link to="/">Home</Link>
          <Link to="/users">Users</Link>
        </div>
      </nav>

      <main className="main-content">
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/users" element={<Users />} />
        </Routes>
      </main>
    </div>
  );
}

export default App;
