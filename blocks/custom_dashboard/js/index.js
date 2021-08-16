import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';

const initApp = () => {
    ReactDOM.render(
        <App/>,
        document.getElementById('app')
    );
};

initApp();
