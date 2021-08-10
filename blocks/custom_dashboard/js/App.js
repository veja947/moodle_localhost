import { QuestionCircleFilled } from '@ant-design/icons';
import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import {Route, Switch} from "react-router";
import CampaignSelector from "./components/CampaignSelector";
import CampaignTable from "./components/CampaignTable";

let table_data = JSON.parse($('#test_test').html());
console.log(table_data.table_records);

class App extends Component {

    render() {
        return (
            <Router>
                <header id="campaign_table_header">
                    <span className="table-title">Student Activity</span>
                    <QuestionCircleFilled  className="title-question-icon" />
                    <div className="table-selector-container">
                        <span className="table-update-date-text">Updated on xxxx-xx-xx</span>
                        <div className="table-selector" >
                            <span className="table-selector-title">View:</span>
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
                </main>
            </Router>
        );
    }
}

export default App
