# Software Requirements Specification (SRS)

## Centralized IoT HVAC Management Platform

**Version:** 2.0\
**Status:** Working baseline --- subject to client validation

## 1. Introduction

### 1.1 Purpose

This SRS defines the functional and non-functional requirements for a
multi-tenant centralized AC management platform. It is intended for the
client, product owner, engineering team, testers, and implementation
agent.

### 1.2 Product scope

The system consists of: - Browser-based organization Manager Portal. -
Backend application/API. - Relational database. - IoT command/event
communication channel. - Device registry. - ESP32 firmware for IR
capture/transmission and local policy evaluation. - Versioned IR profile
catalogue.

Each ESP32 is logically associated with one AC in the initial model. The
AC remains a conventional IR-controlled appliance.

### 1.3 Definitions

-   **Tenant:** Organization whose data is isolated from other
    organizations.
-   **AC:** Infrared-controlled air conditioner.
-   **ESP32:** Per-AC Wi-Fi microcontroller with IR transmitter and
    receiver.
-   **IR profile:** Versioned protocol/model-specific definition for
    representing and transmitting AC states/actions.
-   **Decoded IR data:** Protocol representation produced by a decoder
    when supported.
-   **Raw timings:** Captured pulse/space duration sequence used when
    decoding is unavailable or raw replay is needed.
-   **Known state:** Latest state inferred from recognized events; not
    necessarily verified physical truth.
-   **Device acknowledgement:** ESP32 confirms receipt/processing; not
    proof that the AC executed the command.
-   **ON duration:** Elapsed time derived from known ON/OFF transitions,
    not electricity consumption.

### 1.4 Requirement status

Requirements inherited from the baseline are treated as confirmed unless
explicitly marked proposed/open. The exact set of active controls
remains profile-dependent and must be validated.

## 2. Overall description

### 2.1 Product perspective

The portal, backend, database, IoT messaging layer, device registry, IR
profile library, and ESP32 firmware operate as a connected system. The
backend is responsible for tenant authorization, command orchestration,
event ingestion, history, and reporting. Firmware handles IR and local
behavior.

### 2.2 User class

Initial role: **Organization Administrator / Manager**. This role is
intended for energy/power management personnel and potentially
organization owner/management. Complex RBAC is deferred. A
platform-level support operator is an open operational question.

### 2.3 Operating environment

-   Web browser on desktop and tablet.
-   ESP32 hardware with Wi-Fi, IR transmitter, and IR receiver.
-   Cloud-hosted backend and relational database.
-   Secure device-to-cloud communication.
-   Exact deployment platform and broker remain open.

### 2.4 Constraints

-   One ESP32 per AC in initial deployment.
-   Exact model/remote variant IR validation required.
-   Device online does not prove AC is ON.
-   IR transmission does not prove physical execution.
-   Original remote direct IR path cannot be blocked by ESP32 software
    alone.
-   Electricity/kWh measurement is excluded.
-   Temperature represents setpoint, not measured room temperature.
-   Variant 7 is the Manager Portal UI baseline.

## 3. Functional requirements

### 3.1 Tenant and authentication

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-001                  The system shall        Must
                          support multiple        
                          organizations as        
                          separate tenants.       

  FR-002                  The system shall        Must
                          restrict an             
                          organization            
                          administrator to        
                          resources belonging to  
                          that organization.      

  FR-003                  Tenant scope shall be   Must
                          enforced server-side    
                          for every tenant-owned  
                          resource operation.     

  FR-004                  The system shall        Must
                          authenticate Manager    
                          Portal users and        
                          protect administrative  
                          actions.                

  FR-005                  The initial release     Must
                          shall support one       
                          organization            
                          administrator/manager   
                          role.                   
  -----------------------------------------------------------------------

### 3.2 Location hierarchy

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-006                  The system shall        Must
                          support Organization →  
                          Site → Building → Floor 
                          → nested Space          
                          hierarchy.              

  FR-007                  Spaces shall support    Must
                          configurable types such 
                          as room, corridor,      
                          hall, gallery, section, 
                          or other named area.    

  FR-008                  The administrator shall Must
                          be able to create,      
                          view, update, and       
                          manage location nodes   
                          within their tenant.    

  FR-009                  The portal shall show   Should
                          AC distribution and     
                          status summaries for    
                          selected locations.     
  -----------------------------------------------------------------------

### 3.3 AC asset management

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-010                  The administrator shall Must
                          be able to register AC  
                          assets and assign each  
                          to one physical         
                          location.               

  FR-011                  Each AC shall be        Must
                          associated with         
                          manufacturer, exact     
                          model/remote variant,   
                          and applicable IR       
                          profile where           
                          available.              

  FR-012                  The system shall        Must
                          support multiple        
                          manufacturers and       
                          models through          
                          validated profiles.     

  FR-013                  The initial deployment  Must
                          model shall associate   
                          one ESP32 with one AC.  

  FR-014                  The system shall        Should
                          preserve device-to-AC   
                          assignment identity and 
                          history.                

  FR-015                  The portal shall        Must
                          provide searchable and  
                          filterable AC           
                          inventory.              
  -----------------------------------------------------------------------

### 3.4 Device registry and health

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-016                  The system shall        Must
                          maintain a device       
                          identity and            
                          association with its    
                          tenant and AC.          

  FR-017                  The system shall record Should
                          device firmware version 
                          and last-seen/heartbeat 
                          information where       
                          reported.               

  FR-018                  The system shall        Must
                          display Online,         
                          Offline, or Unknown     
                          connectivity separately 
                          from AC state.          

  FR-019                  Device credentials      Must
                          shall be unique and     
                          revocable.              

  FR-020                  The system shall        Must
                          support device          
                          provisioning and secure 
                          association.            
  -----------------------------------------------------------------------

### 3.5 IR profile catalogue

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-021                  The system shall        Must
                          associate an AC with a  
                          profile validated for   
                          its exact               
                          model/protocol/remote   
                          variant.                

  FR-022                  The profile library     Must
                          shall support decoded   
                          representations and/or  
                          raw IR timings.         

  FR-023                  Profile data shall be   Must
                          versioned.              

  FR-024                  The system shall        Must
                          represent profile       
                          capabilities and        
                          supported setting       
                          ranges.                 

  FR-025                  Profiles shall be       Must
                          tested/validated before 
                          being marked            
                          production-ready.       

  FR-026                  Unknown or unrecognized Must
                          signals shall not       
                          silently alter known    
                          state.                  
  -----------------------------------------------------------------------

### 3.6 AC command control

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-027                  The portal shall allow  Must
                          supported AC operations 
                          according to the        
                          assigned profile        
                          capabilities.           

  FR-028                  The backend shall       Must
                          validate tenant         
                          ownership, AC           
                          capability, requested   
                          values, and device      
                          assignment before       
                          dispatch.               

  FR-029                  The backend shall       Must
                          create a command record 
                          with a unique command   
                          ID and expiry policy.   

  FR-030                  The backend shall route Must
                          a command to the ESP32  
                          assigned to the         
                          selected AC.            

  FR-031                  The system shall record Must
                          command creation,       
                          publication/queue,      
                          device receipt, IR      
                          transmission result,    
                          and errors as distinct  
                          lifecycle facts where   
                          available.              

  FR-032                  The UI shall not        Must
                          present IR transmission 
                          success as proof of     
                          physical AC execution.  

  FR-033                  Offline command         Must
                          behavior and expiry     
                          shall be defined before 
                          enabling queueing.      

  FR-034                  Initial Manager Portal  Must
                          active controls shall   
                          prioritize ON/OFF and   
                          temperature setpoint;   
                          other controls remain   
                          profile- and            
                          scope-dependent.        
  -----------------------------------------------------------------------

### 3.7 Physical remote capture and synchronization

  ------------------------------------------------------------------------
  ID                      Requirement              Priority
  ----------------------- ------------------------ -----------------------
  FR-035                  ESP32 firmware shall     Must
                          capture original remote  
                          signals where hardware   
                          placement and signal     
                          conditions permit.       

  FR-036                  Firmware shall attempt   Must
                          classification using the 
                          configured profile and   
                          preserve                 
                          unknown/decode-failure   
                          outcomes.                

  FR-037                  Firmware shall apply the Must
                          latest locally available 
                          effective                
                          physical-remote policy   
                          to its own signal        
                          processing.              

  FR-038                  Permitted recognized     Must
                          remote actions shall     
                          update local known       
                          configuration and be     
                          reported to backend.     

  FR-039                  Detectable restricted or Should
                          unknown attempts may be  
                          logged with an explicit  
                          classification.          

  FR-040                  Backend shall            Must
                          authenticate, validate,  
                          and deduplicate          
                          device-reported events.  

  FR-041                  The system shall clearly Must
                          communicate that this    
                          software policy cannot   
                          block a handheld         
                          remote's direct IR beam  
                          to the AC.               
  ------------------------------------------------------------------------

### 3.8 Physical remote policy

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-042                  Policy values shall     Must
                          include Inherit,        
                          Enabled, and Disabled.  

  FR-043                  Policy shall be         Must
                          configurable at         
                          supported hierarchy     
                          levels.                 

  FR-044                  An individual AC shall  Must
                          be able to override     
                          inherited policy.       

  FR-045                  The nearest explicit    Must
                          Enabled/Disabled value  
                          shall determine         
                          effective policy.       

  FR-046                  If no explicit value    Must
                          exists, the default     
                          shall be documented and 
                          approved; Disabled is a 
                          proposal, not           
                          confirmed.              

  FR-047                  Policy updates shall    Should
                          synchronize to devices, 
                          with version and        
                          acknowledgement         
                          tracking where          
                          supported.              
  -----------------------------------------------------------------------

### 3.9 Events, state, and history

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-048                  The system shall store  Must
                          timestamped portal      
                          commands, device        
                          events, remote events,  
                          and recognized state    
                          transitions.            

  FR-049                  Each event shall        Must
                          identify source where   
                          known: portal, physical 
                          remote, device/system,  
                          or administrative       
                          correction.             

  FR-050                  State snapshots shall   Must
                          include source,         
                          timestamp, and          
                          quality/freshness       
                          metadata.               

  FR-051                  The system shall        Must
                          distinguish Known ON,   
                          Known OFF, Unknown, and 
                          Stale.                  

  FR-052                  The system shall retain Must
                          device-captured time    
                          and server-received     
                          time where applicable.  

  FR-053                  Event ingestion shall   Must
                          be idempotent using     
                          unique event            
                          identifiers or          
                          equivalent              
                          deduplication keys.     

  FR-054                  The portal shall filter Must
                          history by date,        
                          location, AC, event     
                          type, and source.       
  -----------------------------------------------------------------------

### 3.10 Usage and reporting

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-055                  The system shall        Must
                          calculate ON duration   
                          from recognized ON/OFF  
                          intervals.              

  FR-056                  Incomplete intervals    Must
                          and data gaps shall be  
                          identified.             

  FR-057                  The system shall record Must
                          temperature setpoint    
                          history where           
                          supported.              

  FR-058                  The system shall record Should
                          operating-mode history  
                          where supported.        

  FR-059                  The portal shall        Must
                          provide                 
                          operating-duration      
                          summaries by AC and     
                          location.               

  FR-060                  The system shall not    Must
                          represent ON duration   
                          as electricity          
                          consumption or kWh.     

  FR-061                  The portal shall        Should
                          provide operational     
                          report selection and    
                          export only for         
                          implemented formats.    
  -----------------------------------------------------------------------

### 3.11 Audit and notifications

  -----------------------------------------------------------------------
  ID                      Requirement             Priority
  ----------------------- ----------------------- -----------------------
  FR-062                  Administrative changes  Must
                          to hierarchy, AC        
                          assets, profile         
                          assignments, and        
                          policies shall be       
                          auditable.              

  FR-063                  The portal shall        Should
                          surface relevant        
                          offline, stale-state,   
                          command-failure, and    
                          supported remote-policy 
                          alerts.                 

  FR-064                  Notification            Should
                          read/unread state shall 
                          be maintained if        
                          notifications are       
                          implemented.            
  -----------------------------------------------------------------------

## 4. Business rules

1.  Every AC belongs to one tenant and one physical location.
2.  The initial association model is one ESP32 per AC; device identity
    and assignment must be unique.
3.  An IR profile must match the exact model/protocol/remote variant,
    not merely the brand.
4.  Nearest explicit Enabled/Disabled policy wins; Inherit delegates to
    the parent.
5.  IR transmission is not proof of AC execution.
6.  ON duration is not electricity consumption.
7.  Unknown signals must not be converted into known state changes.
8.  Device online status does not prove AC operating state.
9.  Temperature is a setpoint, not room temperature.

## 5. Non-functional requirements

  -----------------------------------------------------------------------
  ID                      Category                Requirement
  ----------------------- ----------------------- -----------------------
  NFR-001                 Security                Authenticated portal,
                                                  tenant-scoped
                                                  authorization, unique
                                                  device identity,
                                                  encrypted traffic

  NFR-002                 Isolation               Enforce tenant scope
                                                  server-side on every
                                                  resource access

  NFR-003                 Reliability             Idempotent event
                                                  ingestion and safe
                                                  retry/reconnect
                                                  behavior

  NFR-004                 Observability           Track last-seen,
                                                  command lifecycle,
                                                  failures, and
                                                  synchronization health

  NFR-005                 Usability               Navigation follows
                                                  physical hierarchy;
                                                  quick access to AC
                                                  controls/history

  NFR-006                 Scalability             Support growth in
                                                  tenants, locations,
                                                  devices, and event
                                                  volume

  NFR-007                 Maintainability         Separate tenant,
                                                  hierarchy, asset,
                                                  device, IR, command,
                                                  policy, and reporting
                                                  domains

  NFR-008                 Time                    Store timestamps in UTC
                                                  and display in tenant
                                                  timezone

  NFR-009                 Auditability            Trace administrative
                                                  changes and
                                                  device-reported events

  NFR-010                 Responsive UI           Support common desktop
                                                  and tablet viewports

  NFR-011                 Data integrity          Validate references,
                                                  state transitions, and
                                                  command capability

  NFR-012                 Recoverability          Define backup, restore,
                                                  credential revocation,
                                                  and event recovery
                                                  processes
  -----------------------------------------------------------------------

## 6. Data and reporting requirements

  -----------------------------------------------------------------------
  Data                                Meaning / caveat
  ----------------------------------- -----------------------------------
  Known power state                   Latest inferred state from
                                      recognized event/command; not
                                      guaranteed physical truth

  Temperature                         Last known setpoint; not measured
                                      room temperature

  Operating mode                      Last known mode where supported

  Fan/swing                           Last known setting where supported

  Event source                        Portal, physical remote,
                                      device/system, or administrative
                                      correction

  ON duration                         Intervals between known ON/OFF
                                      events; incomplete intervals
                                      flagged

  Device last-seen                    Last successful heartbeat/message

  Electricity                         Not collected in current scope
  -----------------------------------------------------------------------

## 7. External interface requirements

### 7.1 User interface

The Manager Portal shall follow Variant 7: - Icy-blue and white content
surfaces. - Deep-navy sidebar. - Blue primary actions and charts. -
Card-based Locations Explorer; no CAD-like floor plan. - AC control
panel with vertical ON/OFF rocker and temperature setpoint controls. -
Ten screen baseline: Overview, Locations, AC Inventory, AC Control, AC
Details, Usage & Analytics, Activity History, Reports, Notifications,
Organization Settings.

See the separate UI handoff pack for detailed theme and screen
specifications.

### 7.2 Device interface

Device communications shall carry authenticated command,
acknowledgement, transmission-result, physical-remote-event, heartbeat,
and policy-update messages. Exact protocol and broker are open; MQTT
over TLS is a proposed candidate.

### 7.3 Software/API interface

Conceptual endpoints and message shapes are in
`07-API-Contract-Guidelines.md`. They are not finalized contracts until
reviewed against the selected stack and device protocol.

## 8. Acceptance criteria

-   Tenant administrator can sign in and access only tenant resources.
-   Location hierarchy supports nested typed spaces.
-   AC can be registered, assigned a profile, and linked to one ESP32.
-   AC details show last-known settings, device last-seen, and event
    history.
-   Supported portal command reaches the assigned ESP32 and transmission
    attempt is recorded.
-   Supported physical remote operation is
    captured/classified/synchronized when permitted.
-   Policy inheritance and AC override work through the supported
    hierarchy.
-   Restricted/unknown events are logged where detectable.
-   ON duration is calculated and incomplete intervals identified.
-   Setpoint/mode history appears only for supported profiles.
-   No kWh/electricity consumption is inferred from ON duration.
-   Tenant isolation and device identity tests pass.
-   Client explicitly accepts the physical remote direct-IR limitation
    or approves a separate physical mitigation.

## 9. Traceability

Functional requirements are grouped by domain. Detailed test cases
should reference the FR/NFR IDs above. Any implementation change that
removes or weakens a Must requirement requires product-owner approval or
a documented scope change.

## 10. Open requirement decisions

Refer to the Open Decisions Register in `01-Project-Proposal.md` and
maintain a live decision log. Particularly critical: pilot AC models,
remote restriction expectation, default policy, offline command
semantics, device protocol, event retention, OTA scope, and acceptance
definition for IR control.
