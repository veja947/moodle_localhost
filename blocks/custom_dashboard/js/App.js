import { Table } from 'antd';
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
        sorter: (a, b) => a.students - b.students,
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
        sorter: (a, b) => parseFloat(a.rate) - parseFloat(b.rate),
    }
];

let progress_data_fake = [
    {
        name: 'Apples',
        value: 60,
        color: '#eb4d4b'
    },
    {
        name: 'Blueberries',
        value: 17,
        color: '#22a6b3'
    },
    {
        name: 'Guavas',
        value: 23,
        color: '#6ab04c'
    }
];
let table_data_fake = [
    {
        key: '1',
        campaign: 'John Brown',
        students: 32,
        rate: '54%',
        progress: progress_data_fake
    },
    {
        key: '2',
        campaign: 'Jim Green',
        students: 42,
        rate: '98%',
        progress: progress_data_fake
    },
    {
        key: '3',
        campaign: 'Joe Black',
        students: 32,
        rate: '12%',
        progress: progress_data_fake
    },
];

const options = [
    { value: 'program111', label: 'program111' },
    { value: 'program222', label: 'program222' },
    { value: 'program333', label: 'program333' }
];

let table_data = JSON.parse($('#test_test').html());
console.log(table_data);
console.log(table_data_fake);


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
                    <Table
                        columns={table_columns}
                        dataSource={table_data}
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
