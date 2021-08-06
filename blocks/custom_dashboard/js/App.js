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
import MaterialTable from 'material-table';
import ProgressBar from './components/ProgressBar';

const tableIcons = {
    Add: forwardRef((props, ref) => <AddBox {...props} ref={ref} />),
    Check: forwardRef((props, ref) => <Check {...props} ref={ref} />),
    Clear: forwardRef((props, ref) => <Clear {...props} ref={ref} />),
    Delete: forwardRef((props, ref) => <DeleteOutline {...props} ref={ref} />),
    DetailPanel: forwardRef((props, ref) => <ChevronRight {...props} ref={ref} />),
    Edit: forwardRef((props, ref) => <Edit {...props} ref={ref} />),
    Export: forwardRef((props, ref) => <SaveAlt {...props} ref={ref} />),
    Filter: forwardRef((props, ref) => <FilterList {...props} ref={ref} />),
    FirstPage: forwardRef((props, ref) => <FirstPage {...props} ref={ref} />),
    LastPage: forwardRef((props, ref) => <LastPage {...props} ref={ref} />),
    NextPage: forwardRef((props, ref) => <ChevronRight {...props} ref={ref} />),
    PreviousPage: forwardRef((props, ref) => <ChevronLeft {...props} ref={ref} />),
    ResetSearch: forwardRef((props, ref) => <Clear {...props} ref={ref} />),
    Search: forwardRef((props, ref) => <Search {...props} ref={ref} />),
    SortArrow: forwardRef((props, ref) => <ArrowDownward {...props} ref={ref} />),
    ThirdStateCheck: forwardRef((props, ref) => <Remove {...props} ref={ref} />),
    ViewColumn: forwardRef((props, ref) => <ViewColumn {...props} ref={ref} />)
};

const column =[
    { title: 'Campaigns in progress', field: 'campaigns' },
    { title: 'Total students', field: 'total_students', type: 'numeric' },
    {
        title: 'Progress',
        field: 'progress',
        render: rowData => <ProgressBar readings={ rowData.progress } />
    },
    { title: 'Completion rate', field: 'completion_rate' }
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

let data = [
    { campaigns: "program1", total_students: 123, progress: readings_data, completion_rate: "54%" },
    { campaigns: "program2", total_students: 456, progress: readings_data, completion_rate: "12%" },
    { campaigns: "program3", total_students: 321, progress: readings_data, completion_rate: "44%" },
    { campaigns: "program4", total_students: 436, progress: readings_data, completion_rate: "78%" },
    { campaigns: "program5", total_students: 856, progress: readings_data, completion_rate: "99%" },
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
                    <MaterialTable
                        icons={tableIcons}
                        columns={column}
                        data={data}
                        options={{
                            search: true
                        }}
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
