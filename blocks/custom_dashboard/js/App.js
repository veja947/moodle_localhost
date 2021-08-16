import { QuestionCircleFilled } from '@ant-design/icons';
import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import {Route} from "react-router";
import {Switch} from "antd";
import CampaignSelector from "./components/CampaignSelector";
import CampaignTable from "./components/CampaignTable";
import WelcomeBanner from "./components/WelcomeBanner";

let table_data = JSON.parse($('#test_test').html());
console.log(table_data);

class App extends Component {

    constructor() {
        super();
        this.state = {
            isHide : false,
        };
        this.handleToggle = this.handleToggle.bind(this);
    }

    handleToggle() {
        if (this.state.isHide)
        {
            $("#page-header").show();
            this.setState({isHide: false});
        } else {
            $("#page-header").hide();
            this.setState({isHide: true});
        }
    }

    render() {
        return (
            <Router>
                <WelcomeBanner />
                <CampaignTable
                    dataSource={ table_data }
                />
                <Switch checked={this.state.isHide} onChange={this.handleToggle} />
            </Router>
        );
    }
}

export default App
