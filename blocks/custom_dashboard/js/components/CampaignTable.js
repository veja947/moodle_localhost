import React from "react";
import { Table } from "antd";
import ProgressBar from "./ProgressBar";
import {QuestionCircleFilled} from "@ant-design/icons";
import CampaignSelector from "./CampaignSelector";


const table_columns = [
    {
        title: 'All active campaigns',
        dataIndex: 'campaign',
        key: 'campaign',
        render: (text, record) => <a
            href={'/admin/tool/program/edit.php?id=' + record.key}
            className='campaign-name-link'>{text}</a>,
    },
    {
        title: 'Total students',
        dataIndex: 'students',
        key: 'students',
        sorter: (a, b) => a.students - b.students,
    },
    {
        title: () => {
            return (
                <div>
                    Progress
                    <div className="progress-icons-container">
                        <div className="progress-icon">
                            <span className="icon-dot completed-icon-dot"> </span>
                            <span className="completed-icon-label">Completed</span>
                        </div>
                        <div className="progress-icon">
                            <span className="icon-dot in-progress-icon-icon-dot"> </span>
                            <span className="in-progress-icon-icon-label">In progress</span>
                        </div>
                        <div className="progress-icon">
                            <span className="icon-dot not-started-icon-icon-dot"> </span>
                            <span className="not-started-icon-icon-label">Not started</span>
                        </div>
                    </div>
                </div>
            );
        },
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

export default class CampaignTable extends React.Component {
    constructor(props) {
        super(props);

        this.columns = table_columns;
        this.dataSource = props.dataSource;
        this.state = {
            error: null,
            isLoading: false,
            tableData: this.dataSource.table_records,
        };

        this.rerenderParentCallback = this.rerenderParentCallback.bind(this);
    }

    rerenderParentCallback(value) {
        this.columns[0]['title'] = value ? 'Modules' : 'All active campaigns';
        this.setState({ tableData: value ? this.dataSource.module_records[value] : this.dataSource.table_records });
        this.forceUpdate();
    }

    componentDidMount() {
        fetch("https://reqres.in/api/users/2")
            .then(res => res.json())
            .then(
                (result) => {
                },
                // Note: it's important to handle errors here
                // instead of a catch() block so that we don't swallow
                // exceptions from actual bugs in components.
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            );
    }

    render() {
        console.log(`Table rendered.`);
        return (
            <div>
                <header id="campaign_table_header">
                    <span className="table-title">Student Activity</span>
                    <QuestionCircleFilled  className="title-question-icon" />
                    <div className="table-selector-container">
                        <span className="table-update-date-text">Updated on xxxx-xx-xx</span>
                        <div className="table-selector" >
                            <span className="table-selector-title">View:</span>
                            <CampaignSelector
                                options={ this.dataSource.selector_records }
                                rerenderParentCallback={ this.rerenderParentCallback }
                            />
                        </div>
                    </div>
                </header>
                <main>
                    <Table
                        columns={ this.columns }
                        dataSource={ this.state.tableData }
                        pagination={{ defaultPageSize: 3, showSizeChanger: true, pageSizeOptions: ['3', '5', '10']}}
                    />
                </main>
            </div>

        );
    }
}