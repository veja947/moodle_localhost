import { Table } from 'antd';
import { forwardRef } from 'react';
import AddBox from '@material-ui/icons/AddBox';
import ArrowDownward from '@material-ui/icons/ArrowDownward';
import Check from '@material-ui/icons/Check';
import ChevronLeft from '@material-ui/icons/ChevronLeft';
import ChevronRight from '@material-ui/icons/ChevronRight';
import Clear from '@material-ui/icons/Clear';
import DeleteOutline from '@material-ui/icons/DeleteOutline';
import Edit from '@material-ui/icons/Edit';
import FilterList from '@material-ui/icons/FilterList';
import FirstPage from '@material-ui/icons/FirstPage';
import LastPage from '@material-ui/icons/LastPage';
import Remove from '@material-ui/icons/Remove';
import SaveAlt from '@material-ui/icons/SaveAlt';
import Search from '@material-ui/icons/Search';
import ViewColumn from '@material-ui/icons/ViewColumn';


import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import {Route, Switch} from "react-router";
import Select from 'react-select';
import ProgressBar from './components/ProgressBar';

let table_columns = [
    {
        title: 'Campaigns in progress',
        dataIndex: 'campaign',
        key: 'campaign',
        render: text => <a class='campaign-name-link'>{text}</a>,
    },
    {
        title: 'Total students',
        dataIndex: 'students',
        key: 'students',
    },
    {
        title: 'Progress',
        key: 'progress',
        dataIndex: 'progress',
        render: ( cell, row ) => { return (<ProgressBar readings={ row.progress } />) },
    },
    {
        title: 'Completion rate',
        dataIndex: 'rate',
        key: 'rate',
    }
];

let readings_data = [
    {
        name: 'Apples',
        value: 60,
        color: '#eb4d4b'
    },
    {
        name: 'Blueberries',
        value: 7,
        color: '#22a6b3'
    },
    {
        name: 'Guavas',
        value: 23,
        color: '#6ab04c'
    },
    {
        name: 'Grapes',
        value: 10,
        color: '#e056fd'
    }
];
let table_data = [
    {
        key: '1',
        campaign: 'John Brown',
        students: 32,
        rate: '54%',
        progress: readings_data
    },
    {
        key: '2',
        campaign: 'Jim Green',
        students: 42,
        rate: '98%',
        progress: readings_data
    },
    {
        key: '3',
        campaign: 'Joe Black',
        students: 32,
        rate: '12%',
        progress: readings_data
    },
];

const options = [
    { value: 'program111', label: 'program111' },
    { value: 'program222', label: 'program222' },
    { value: 'program333', label: 'program333' }
];

let activity_data = JSON.parse($('#test_test').html())


class App extends Component {


    render() {
        return (
            <Router>
                <header>
                    <div>
                        <p>Student Activity</p>
                    </div>
                    <div>
                        <p>Updated on xxxx-xx-xx</p>
                        <div>
                            <Select options={options} />
                        </div>
                    </div>
                </header>
                <main>
                    <ProgressBar readings={ readings_data } />
                    <Table
                        columns={table_columns}
                        dataSource={table_data}
                    />
                    <Switch>
                        <Route path="/">
                            <h5>new Dashboard</h5>
                        </Route>
                    </Switch>
                </main>
            </Router>
        );
    }
}

export default App
