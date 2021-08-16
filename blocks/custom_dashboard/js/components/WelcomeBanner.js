import React from "react";
import {Row, Col, Divider} from "antd";
import {Icon} from '@ant-design/icons';
import WelcomeSvg from "../../img/welcome_icon.svg";


export default class WelcomeBanner extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Row id="welcome_banner_container">
                <Col className="welcome-text-container"
                     xs={{span: 24, offset: 0}}
                     lg={{span: 16, offset: 0}}>
                    <div className="welcome-logo">
                        {/*<WelcomeSvg className="icon-primary"/>*/}
                        {/*<Icon component={WelcomeSvg} />*/}
                    </div>
                    <div className="welcome-text">
                        <div className="welcome-title">Good morning Sherry!</div>
                        <div className="welcome-content">You can find the overview of program activities, module
                            activities, and Fortiphish campaigns here.
                        </div>
                    </div>
                </Col>

                <Col className="welcome-data"
                     xs={{span: 24, offset: 0}}
                     lg={{span: 8, offset: 0}}>
                    <div className="total-campaigns-container">
                        <div className="total-campaigns-title">
                            Total Launched Campaigns
                        </div>
                        <div className="total-campaigns-number">5</div>
                    </div>
                    <Divider type="vertical" className="welcome-data-divider"/>
                    <div className="total-students-container">
                        <div className="total-students-title">
                            Total Students
                        </div>
                        <div className="total-students-number">432</div>
                    </div>
                </Col>
            </Row>
        );
    }
}