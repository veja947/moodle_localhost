import { QuestionCircleFilled } from '@ant-design/icons';
import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import {Route} from "react-router";
import {Switch} from "antd";
import CampaignSelector from "./components/CampaignSelector";
import CampaignTable from "./components/CampaignTable";
import WelcomeBanner from "./components/WelcomeBanner";
import TestCode from "./components/TestCode";

let table_data = JSON.parse($('#test_test').html());
console.log(table_data);

class App extends Component {

    constructor() {
        super();
    }
    render() {
        return (
            <div>
                <WelcomeBanner />
                <CampaignTable
                    dataSource={ table_data }
                />
                <TestCode />
            </div>
        );
    }
}

export default App
