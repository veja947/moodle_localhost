import { QuestionCircleFilled } from '@ant-design/icons';
import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import {Route, Switch} from "react-router";
import CampaignSelector from "./components/CampaignSelector";
import CampaignTable from "./components/CampaignTable";

let table_data = JSON.parse($('#test_test').html());
console.log(table_data);

class App extends Component {

    render() {
        return (
            <Router>
                <CampaignTable
                    dataSource={ table_data }
                />
            </Router>
        );
    }
}

export default App
