import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import {Route, Switch} from "react-router";

class App extends Component {
    render() {
        return (
            <Router>
                <main>
                    <Switch>
                        <Route path="/">
                            <h1>new Dashboard</h1>
                        </Route>
                    </Switch>
                </main>
            </Router>
        );
    }
}

export default App
