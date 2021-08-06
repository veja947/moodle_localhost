import { Table, Select } from 'antd';
import { Option } from 'rc-select';
import React, {Component} from 'react';
import {BrowserRouter as Router} from 'react-router-dom';
import {Route, Switch} from "react-router";
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
        width: '40%',
        render: ( cell, row ) => { return (<ProgressBar readings={ row.progress } />) },
    },
    {
        title: 'Completion rate',
        dataIndex: 'rate',
        key: 'rate',
        sorter: (a, b) => parseFloat(a.rate) - parseFloat(b.rate),
    }
];

let table_data = JSON.parse($('#test_test').html());
console.log(table_data);


function onChange(value) {
    console.log(`selected ${value}`);
}

function onBlur() {
    console.log('blur');
}

function onFocus() {
    console.log('focus');
}

function onSearch(val) {
    console.log('search:', val);
}

class App extends Component {


    render() {
        return (
            <Router>
                <header>
                    <span>Student Activity</span>
                    <div style={{float: "right"}}>
                        <span>Updated on xxxx-xx-xx</span>
                        <div style={{display: "inline-block"}}>
                            <Select
                                showSearch
                                allowClear
                                style={{ width: 200 }}
                                placeholder="Select a person"
                                optionFilterProp="children"
                                onChange={onChange}
                                onFocus={onFocus}
                                onBlur={onBlur}
                                onSearch={onSearch}
                                filterOption={(input, option) =>
                                    option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                                }
                            >
                                <Option value="jack">Jack</Option>
                                <Option value="lucy">Lucy</Option>
                                <Option value="tom">Tom</Option>
                            </Select>
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
