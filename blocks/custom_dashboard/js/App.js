import { Table } from 'antd';
import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import {Route, Switch} from "react-router";
import ProgressBar from './components/ProgressBar';
import CampaignSelector from "./components/CampaignSelector";
import CampaignTable from "./components/CampaignTable";

let table_data = JSON.parse($('#test_test').html());
console.log(table_data.selector_records);

class App extends Component {

    render() {
        return (
            <Router>
                <header>
                    <span>Student Activity</span>
                    <div style={{float: "right"}}>
                        <span style={{margin: "auto 10px"}}>Updated on xxxx-xx-xx</span>
                        <div style={{display: "inline-block"}}>
                            <CampaignSelector
                                options={table_data.selector_records}
                            />
                        </div>
                    </div>
                </header>
                <main>
                    <CampaignTable
                        dataSource={table_data.table_records}
                    />
                    <Switch>
                        <Route path="/">
                            <h5></h5>
                        </Route>
                    </Switch>
                </main>
            </Router>
        );
    }
}

export default App
