import React from "react";
import { Table, Spin, Switch } from "antd";
import { LoadingOutlined} from "@ant-design/icons";
import ProgressBar from "./ProgressBar";
import {QuestionCircleFilled} from "@ant-design/icons";
import CampaignSelector from "./CampaignSelector";

const table_columns = [
    {
        title: 'All active campaigns',
        dataIndex: 'campaign',
        key: 'campaign',
        render: (text, record) => {
            return record.is_module
                ? <a href={'/course/view.php?id=' + record.key}
                                  className='module-name-link' data-id={record.key}>{text}</a>
                : <a href={'/admin/tool/program/edit.php?id=' + record.key}
                     className='campaign-name-link' data-id={record.key}>{text}</a>
        },
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
        this.newDate = new Date();

        this.state = {
            error: null,
            isLoading: true,
            tableData: this.dataSource.table_records,
            updateDate: `${this.newDate.getFullYear()}-${this.newDate.getMonth() + 1}-${this.newDate.getDate()}`,
        };

        this.rerenderParentCallback = this.rerenderParentCallback.bind(this);
    }

    rerenderParentCallback(value) {
        this.columns[0]['title'] = value ? 'Modules' : 'All active campaigns';
        this.setState({ tableData: value ? this.dataSource.module_records[value] : this.dataSource.table_records });
    }

    componentDidMount() {
        this.setState({isLoading: false});
    }

    render() {
        return (
            <div id="campaign_table_container">
                <div id="campaign_table_header">
                    <span className="table-title">Student Activity</span>
                    <QuestionCircleFilled  className="title-question-icon" />
                    <div className="table-selector-container">
                        <span className="table-update-date-text">Updated on {this.state.updateDate}</span>
                        <div className="table-selector" >
                            <span className="table-selector-title">View:</span>
                            <CampaignSelector
                                options={ this.dataSource.selector_records }
                                rerenderParentCallback={ this.rerenderParentCallback }
                            />
                        </div>
                    </div>
                </div>
                <div id="campaign_table_main">
                    <Table
                        // locale={{ emptyText: 'No student campaign has been set' }}
                        columns={ this.columns }
                        dataSource={ this.state.tableData }
                        pagination={{ defaultPageSize: 5}}
                        loading={ this.state.isLoading }
                    />
                </div>
            </div>

        );
    }
}