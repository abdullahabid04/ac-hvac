#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <EEPROM.h>
#include <Arduino.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClientSecure.h>
#include <ArduinoJson.h>
#include <IRremoteESP8266.h>
#include <IRsend.h>
#include <ir_Gree.h>
#include "DHTesp.h"
#include <time.h>  // for ISO 8601 timestamps

/////////////////////////////////////////////////////////
// Environment switch
// Set USE_PRODUCTION to true to test against https://hvac.cosmosalgos.com
// Set USE_PRODUCTION to false for local testing
#define USE_PRODUCTION false

#if USE_PRODUCTION
  // Production backend (HTTPS)
  const char* baseUrl = "https://hvac.cosmosalgos.com/api/v1";
#else
  // IMPORTANT: 127.0.0.1 does NOT work on the ESP device.
  // Replace with your computer's LAN IP (e.g., "http://192.168.1.50:8000/api/v1").
  // Find your PC's IP with: ipconfig (Windows) or ifconfig (Mac/Linux)
  const char* baseUrl = "http://192.168.1.100:8000/api/v1";  // <-- CHANGE THIS TO YOUR PC's IP
#endif

/////////////////////////////////////////////////////////

const char* ssid = "PRO-AK-004";
const char* password = "AK004678";
/////////////////////////////////////////////////////////

HTTPClient https;

DHTesp dht;
bool send = false;
bool electricity_status = 0;

// Device identity — serial-number-only authentication.
// This must match a registered, active device in the AC Management backend.
// Format: HVAC-ESP32-YYYY-NNNNNN
const char* deviceSerialNumber = "HVAC-ESP32-2026-000001";
const char* deviceUid = "ESP32-AC-LECTURE-01";
/////////////////////////////////////////////////////////
const uint16_t kIrLed = 4;  // GPIO pin to use. Recommended: 4 (D2).
IRGreeAC ac(kIrLed, YBOFB, true, false);

// Simulated AC state
int ACTemp = 16;
bool ACStatus = false;

// Timing — 10 second cycle
unsigned long previousMillis = 0;
const unsigned long cycleInterval = 10000;  // 10 seconds

/////////////////////////////////////////////////////////
// RTD (thermistor) — kept from original
#define SERIESRESISTOR 10000
#define THERMISTORPIN A0
int sensorValue = 0;
int total = 0;
float average = 0.0;
float temp_1 = 0;

///////////////////////////////////////////////////////////
ESP8266WebServer server(80);

String wifiCredentials;
String newSsid;
String newPassword;
String newBlock;
String newRoom;
String a1;
String a2;
String a3;
String a4;
String a5;
String temp_str;
String humidity_str;

bool wifi_status_connection = false;
bool server_connection_status = false;
bool internet_connection_status = false;
bool mode;

uint8_t ipArray[4];
uint8_t subnetArray[4];
uint8_t gatewayArray[4];
uint8_t dns1Array[4];
uint8_t dns2Array[4];

void setup() {
  pinMode(0, OUTPUT);
  digitalWrite(0, LOW);
  pinMode(15, OUTPUT);
  tone_gen(5);

  dht.setup(5, DHTesp::DHT22);
  delay(200);
  ac.begin();
  ac.setMode(kGreeCool);
  printState();

  ac.setTemp(20);
  delay(200);

  Serial.begin(9600);
  WiFi.disconnect();
  EEPROM.begin(512);
  WiFi.mode(WIFI_AP_STA);
  WiFi.softAP(ssid, password);

  server.on("/", handleRoot);
  server.on("/pro", handlepro);
  server.on("/pro1", handlepro1);
  server.on("/setup", handleSetup);
  server.on("/setup1", handleSetup1);
  server.on("/info", handleInfo);
  server.begin();

  EEPROM.begin(256);
  load_strings();
  Serial.println("EEPROM.read WiFi mode:");
  Serial.println(mode);

  // Initialize time for ISO 8601 timestamps (required by backend)
  configTime(0, 0, "pool.ntp.org");

  if (mode == false) {
    wifi_connect_dynamic();
  }
  if (mode == true) {
    wifi_connect_static();
  }
}

void store_dynamic_strings() {
  for (int i = 0; i < 204; ++i) {
    EEPROM.write(i, 0);
  }
  for (int i = 0; i < newSsid.length(); ++i) {
    EEPROM.write(i, newSsid[i]);
  }
  for (int i = 0; i < newPassword.length(); ++i) {
    EEPROM.write(32 + i, newPassword[i]);
  }
  a1 = "Not set";
  for (int i = 0; i < a1.length(); ++i) {
    EEPROM.write(64 + i, a1[i]);
  }
  a2 = "Not set";
  for (int i = 0; i < a2.length(); ++i) {
    EEPROM.write(79 + i, a2[i]);
  }
  a3 = "Not set";
  for (int i = 0; i < a3.length(); ++i) {
    EEPROM.write(94 + i, a3[i]);
  }
  a4 = "Not set";
  for (int i = 0; i < a4.length(); ++i) {
    EEPROM.write(109 + i, a4[i]);
  }
  a5 = "Not set";
  for (int i = 0; i < a5.length(); ++i) {
    EEPROM.write(124 + i, a5[i]);
  }
  for (int i = 0; i < newBlock.length(); ++i) {
    EEPROM.write(139 + i, newBlock[i]);
  }
  for (int i = 0; i < newRoom.length(); ++i) {
    EEPROM.write(171 + i, newRoom[i]);
  }
  EEPROM.commit();
}
void store_static_strings() {
  for (int i = 0; i < 204; ++i) {
    EEPROM.write(i, 0);
  }
  for (int i = 0; i < newSsid.length(); ++i) {
    EEPROM.write(i, newSsid[i]);
  }
  for (int i = 0; i < newPassword.length(); ++i) {
    EEPROM.write(32 + i, newPassword[i]);
  }
  for (int i = 0; i < a1.length(); ++i) {
    EEPROM.write(64 + i, a1[i]);
  }
  for (int i = 0; i < a2.length(); ++i) {
    EEPROM.write(79 + i, a2[i]);
  }
  for (int i = 0; i < a3.length(); ++i) {
    EEPROM.write(94 + i, a3[i]);
  }
  for (int i = 0; i < a4.length(); ++i) {
    EEPROM.write(124 + i, a4[i]);
  }
  for (int i = 0; i < a5.length(); ++i) {
    EEPROM.write(139 + i, a5[i]);
  }
  for (int i = 0; i < newBlock.length(); ++i) {
    EEPROM.write(139 + i, newBlock[i]);
  }
  for (int i = 0; i < newRoom.length(); ++i) {
    EEPROM.write(171 + i, newRoom[i]);
  }
  EEPROM.commit();
}
void load_strings() {
  for (int i = 0; i < 32; ++i) {
    newSsid += char(EEPROM.read(i));
  }
  for (int i = 32; i < 64; ++i) {
    newPassword += char(EEPROM.read(i));
  }
  for (int i = 64; i < 79; ++i) {
    a1 += char(EEPROM.read(i));
  }
  for (int i = 79; i < 94; ++i) {
    a2 += char(EEPROM.read(i));
  }
  for (int i = 94; i < 109; ++i) {
    a3 += char(EEPROM.read(i));
  }
  for (int i = 109; i < 124; ++i) {
    a4 += char(EEPROM.read(i));
  }
  for (int i = 124; i < 139; ++i) {
    a5 += char(EEPROM.read(i));
  }
  for (int i = 139; i < 171; ++i) {
    newBlock += char(EEPROM.read(i));
  }
  for (int i = 171; i < 203; ++i) {
    newRoom += char(EEPROM.read(i));
  }
  mode = EEPROM.read(204) == 1;
}

// ------------------------------------------------------------
// Main loop — 10 second cycle
// ------------------------------------------------------------
void loop() {
  unsigned long currentMillis = millis();

  server.handleClient();

  if (currentMillis - previousMillis >= cycleInterval) {
    previousMillis = currentMillis;

    if (WiFi.status() == WL_CONNECTED) {
      digitalWrite(0, LOW);

      // 1. Ping server: heartbeat + update current state
      sendHeartbeat();
      reportState();

      // 2. Ping again: get latest command, perform it, update action performed
      pollAndExecuteCommands();
    } else {
      Serial.println("WiFi Disconnected. Reconnecting...");
      digitalWrite(0, HIGH);
      if (!mode) wifi_connect_dynamic();
      else wifi_connect_static();
    }
  }
}

// ------------------------------------------------------------
// Web UI handlers — unchanged from original
// ------------------------------------------------------------
void handleRoot() {
  Serial.print("Heap before HTML response: ");
  Serial.println(ESP.getFreeHeap());

  String message;
  message.reserve(3000);

  message += "<!DOCTYPE html><html lang='en'><head>";
  message += "<meta charset='UTF-8'>";
  message += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  message += "<title>Configuration</title>";
  message += "<style>";
  message += "body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; display: flex; flex-direction: row; min-height: 100vh; }";
  message += ".sidebar { width: 250px; background-color: #2c3e50; color: #ecf0f1; padding: 1em; display: flex; flex-direction: column; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }";
  message += ".sidebar h1 { font-size: 1.5em; margin-bottom: 1em; }";
  message += ".sidebar button { background-color: #34495e; border: none; color: white; padding: 0.8em; margin: 0.5em 0; text-align: left; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }";
  message += ".sidebar button:hover { background-color: #1abc9c; }";
  message += ".content { flex: 1; padding: 2em; }";
  message += ".content h2 { margin-top: 0; }";
  message += ".content p { font-size: 1rem; background: #fff; padding: 1em; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); max-width: 700px; margin: 0 auto; }";
  message += "@media (max-width: 768px) { body { flex-direction: column; } .sidebar { width: 100%; flex-direction: row; overflow-x: auto; } .sidebar h1 { display: none; } .sidebar button { flex: 1; margin: 0.3em; font-size: 0.9em; } .content { padding: 1em; } .content p { font-size: 0.9em; } }";
  message += "</style></head><body>";

  message += "<div class='sidebar'>";
  message += "<h1>AIRWISE <i>Pro</i></h1>";
  message += "<button onclick=\"window.location.href='/info'\">Diagnostic</button>";
  message += "<button onclick=\"window.location.href='/pro'\">WiFi (Dynamic)</button>";
  message += "<button onclick=\"window.location.href='/pro1'\">WiFi (Static)</button>";
  message += "</div>";

  message += "<div class='content'>";
  message += "<h2>Configuration Console</h2>";
  message += "<p><strong>1 -</strong> Generate the diagnostic report by clicking the <strong>Diagnostic</strong> button.<br><br>";
  message += "<strong>2 -</strong> Configure WiFi dynamically using SSID + Password only.<br><br>";
  message += "<strong>3 -</strong> Configure WiFi with static IP for advanced setup.</p>";
  message += "</div></body></html>";

  server.send(200, "text/html", message);
}

void handlepro() {
  Serial.print("Heap before HTML response: ");
  Serial.println(ESP.getFreeHeap());

  String message;
  message.reserve(3000);

  message += "<!DOCTYPE html><html lang='en'><head>";
  message += "<meta charset='UTF-8'>";
  message += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  message += "<title>WiFi Configuration</title>";
  message += "<style>";
  message += "body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; display: flex; flex-direction: row; flex-wrap: wrap; min-height: 100vh; }";
  message += ".sidebar { width: 240px; background-color: #2c3e50; color: #ecf0f1; padding: 1.5em; display: flex; flex-direction: column; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }";
  message += ".sidebar h1 { font-size: 1.4em; margin-bottom: 1em; }";
  message += ".sidebar button { background-color: #34495e; border: none; color: white; padding: 0.8em; margin: 0.4em 0; text-align: left; border-radius: 6px; cursor: pointer; font-size: 1rem; }";
  message += ".sidebar button:hover { background-color: #1abc9c; }";
  message += ".content { flex: 1; padding: 2em; display: flex; align-items: center; justify-content: center; }";
  message += ".form-container { background: #fff; padding: 2em; width: 100%; max-width: 600px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }";
  message += ".form-container h2 { text-align: center; color: #2c3e50; }";
  message += "form { display: flex; flex-direction: column; }";
  message += "label { margin-top: 1em; font-weight: 600; color: #333; }";
  message += "input { padding: 0.6em; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }";
  message += ".note { font-size: 0.8rem; color: #666; margin-top: 0.3em; }";
  message += "button.submit { margin-top: 1.5em; background-color: #007bff; color: white; border: none; padding: 0.8em; border-radius: 6px; font-size: 1rem; cursor: pointer; }";
  message += "button.submit:hover { background-color: #0056b3; }";
  message += "@media (max-width: 768px) { body { flex-direction: column; } .sidebar { width: 100%; flex-direction: row; flex-wrap: wrap; justify-content: space-around; box-shadow: none; } .sidebar h1 { width: 100%; text-align: center; font-size: 1.2em; margin-bottom: 0.5em; } .sidebar button { width: 100%; text-align: center; font-size: 1em; padding: 0.6em; } .content { padding: 1em; } .form-container { padding: 1em; } }";
  message += "</style></head><body>";

  message += "<div class='sidebar'>";
  message += "<h1>AIRWISE <i>Pro</i></h1>";
  message += "<button onclick=\"window.location.href='/info'\">IRIS Diagnostic</button>";
  message += "<button onclick=\"window.location.href='/pro'\">WiFi (Dynamic IP)</button>";
  message += "<button onclick=\"window.location.href='/pro1'\">WiFi (Static IP)</button>";
  message += "</div>";

  message += "<div class='content'>";
  message += "<div class='form-container'>";
  message += "<h2>WiFi Configuration</h2>";
  message += "<form action='/setup' method='post'>";
  message += "<label for='ssid'>SSID:</label>";
  message += "<input type='text' id='ssid' name='ssid' placeholder='Enter your WiFi SSID' required>";
  message += "<div class='note'>(Max. 32 Characters)</div>";

  message += "<label for='password'>Password:</label>";
  message += "<input type='password' id='password' name='password' placeholder='Enter WiFi Password' required>";
  message += "<div class='note'>(Max. 32 Characters)</div>";

  message += "<label for='Block'>Install Location (Building):</label>";
  message += "<input type='text' id='Block' name='Block' placeholder='Enter the building name' required>";
  message += "<div class='note'>(Max. 32 Characters)</div>";

  message += "<label for='Room'>Install Location (Room):</label>";
  message += "<input type='text' id='Room' name='Room' placeholder='Enter the room name' required>";
  message += "<div class='note'>(Max. 32 Characters)</div>";

  message += "<button type='submit' class='submit'>Submit</button>";
  message += "</form></div></div></body></html>";

  server.send(200, "text/html", message);
}

void handlepro1() {
  Serial.print("Heap before HTML response: ");
  Serial.println(ESP.getFreeHeap());
  String message;
  message.reserve(3000);

  message += "<!DOCTYPE html><html lang='en'><head>";
  message += "<meta charset='UTF-8'>";
  message += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  message += "<title>Static IP Configuration</title>";
  message += "<style>";
  message += "body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; display: flex; flex-direction: row; flex-wrap: wrap; min-height: 100vh; }";
  message += ".sidebar { width: 240px; background-color: #2c3e50; color: #ecf0f1; padding: 1.5em; display: flex; flex-direction: column; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }";
  message += ".sidebar h1 { font-size: 1.4em; margin-bottom: 1em; }";
  message += ".sidebar button { background-color: #34495e; border: none; color: white; padding: 0.8em; margin: 0.4em 0; text-align: left; border-radius: 6px; cursor: pointer; font-size: 1rem; }";
  message += ".sidebar button:hover { background-color: #1abc9c; }";
  message += ".content { flex: 1; padding: 2em; display: flex; align-items: center; justify-content: center; }";
  message += ".form-container { background: #fff; padding: 2em; width: 100%; max-width: 600px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }";
  message += ".form-container h2 { text-align: center; color: #2c3e50; }";
  message += "form { display: flex; flex-direction: column; }";
  message += "label { margin-top: 1em; font-weight: 600; color: #333; }";
  message += "input { padding: 0.6em; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }";
  message += ".note { font-size: 0.8rem; color: #666; margin-top: 0.3em; }";
  message += "button.submit { margin-top: 1.5em; background-color: #007bff; color: white; border: none; padding: 0.8em; border-radius: 6px; font-size: 1rem; cursor: pointer; }";
  message += "button.submit:hover { background-color: #0056b3; }";
  message += "@media (max-width: 768px) { body { flex-direction: column; } .sidebar { width: 100%; flex-direction: row; flex-wrap: wrap; justify-content: space-around; box-shadow: none; } .sidebar h1 { width: 100%; text-align: center; font-size: 1.2em; margin-bottom: 0.5em; } .sidebar button { width: 100%; text-align: center; font-size: 1em; padding: 0.6em; } .content { padding: 1em; } .form-container { padding: 1em; } }";
  message += "</style></head><body>";

  message += "<div class='sidebar'>";
  message += "<h1>AIRWISE <i>Pro</i></h1>";
  message += "<button onclick=\"window.location.href='/info'\">IRIS Diagnostic</button>";
  message += "<button onclick=\"window.location.href='/pro'\">WiFi (Dynamic IP)</button>";
  message += "<button onclick=\"window.location.href='/pro1'\">WiFi (Static IP)</button>";
  message += "</div>";

  message += "<div class='content'>";
  message += "<div class='form-container'>";
  message += "<h2>Static IP Configuration</h2>";
  message += "<form action='/setup1' method='post'>";

  message += "<label for='ssid'>SSID:</label>";
  message += "<input type='text' id='ssid' name='ssid' placeholder='Enter your WiFi SSID' required>";
  message += "<div class='note'>(Max. 32 Characters)</div>";

  message += "<label for='password'>Password:</label>";
  message += "<input type='password' id='password' name='password' placeholder='Enter WiFi Password' required>";
  message += "<div class='note'>(Max. 32 Characters)</div>";

  message += "<label for='ip'>IP Address:</label>";
  message += "<input type='text' id='ip' name='ip' placeholder='192.168.10.3'>";

  message += "<label for='subnet'>Subnet Mask:</label>";
  message += "<input type='text' id='subnet' name='subnet' placeholder='255.255.255.0'>";

  message += "<label for='gateway'>Gateway:</label>";
  message += "<input type='text' id='gateway' name='gateway' placeholder='192.168.10.1'>";

  message += "<label for='primaryDns'>Primary DNS:</label>";
  message += "<input type='text' id='primaryDns' name='primaryDns' placeholder='8.8.8.8'>";
  message += "<div class='note'>(Optional)</div>";

  message += "<label for='secondaryDns'>Secondary DNS:</label>";
  message += "<input type='text' id='secondaryDns' name='secondaryDns' placeholder='8.8.4.4'>";
  message += "<div class='note'>(Optional)</div>";

  message += "<label for='Block'>Install Location (Building):</label>";
  message += "<input type='text' id='Block' name='Block' placeholder='Enter the building name' required>";
  message += "<div class='note'>(Max. 32 Characters)</div>";

  message += "<label for='Room'>Install Location (Room):</label>";
  message += "<input type='text' id='Room' name='Room' placeholder='Enter the room name' required>";
  message += "<div class='note'>(Max. 32 Characters)</div>";

  message += "<button type='submit' class='submit'>Submit</button>";
  message += "</form></div></div></body></html>";
  server.send(200, "text/html", message);
}

void handleInfo() {
  Serial.print("Heap before HTML response: ");
  Serial.println(ESP.getFreeHeap());
  String message;
  message.reserve(3000);

  message += "<!DOCTYPE html><html lang='en'><head>";
  message += "<meta charset='UTF-8'>";
  message += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  message += "<title>Diagnostic</title>";
  message += "<style>";
  message += "body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; display: flex; flex-direction: row; align-items: flex-start; min-height: 100vh; flex-wrap: nowrap; }";
  message += ".sidebar { width: 240px; background-color: #2c3e50; color: #ecf0f1; padding: 1.5em; display: flex; flex-direction: column; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }";
  message += ".sidebar h1 { font-size: 1.4em; margin-bottom: 1em; }";
  message += ".sidebar button { background-color: #34495e; border: none; color: white; padding: 0.8em; margin: 0.4em 0; text-align: left; border-radius: 6px; cursor: pointer; font-size: 1rem; }";
  message += ".sidebar button:hover { background-color: #1abc9c; }";
  message += ".content { flex: 1; padding: 2em; display: flex; flex-direction: column; align-items: center; }";
  message += ".form-container { background: #fff; padding: 2em; width: 100%; max-width: 600px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }";
  message += ".form-container h2 { text-align: center; color: #2c3e50; }";
  message += "form { display: flex; flex-direction: column; }";
  message += "label { margin-top: 1em; font-weight: 600; color: #333; }";
  message += "input { padding: 0.6em; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }";
  message += ".note { font-size: 0.8rem; color: #666; margin-top: 0.3em; }";
  message += "button.submit { margin-top: 1.5em; background-color: #007bff; color: white; border: none; padding: 0.8em; border-radius: 6px; font-size: 1rem; cursor: pointer; }";
  message += "button.submit:hover { background-color: #0056b3; }";
  message += ".section { background: #fff; padding: 1.5em; width: 100%; max-width: 600px; margin-bottom: 2em; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }";
  message += ".section table { width: 100%; border-collapse: collapse; }";
  message += ".section th, .section td { text-align: left; padding: 0.5em; font-size: 0.95em; color: #333; }";
  message += ".section th { width: 40%; font-weight: 600; }";
  message += "table { width: 100%; border-collapse: collapse; }";
  message += "th, td { text-align: left; padding: 0.5em; font-size: 0.95em; color: #333; }";
  message += "th { width: 40%; font-weight: 600; }";

  message += "@media (max-width: 768px) {";
  message += "  body { flex-direction: column; }";
  message += "  .content { padding: 1em; }";
  message += "  .section { padding: 1em; margin-bottom: 1.5em; }";
  message += "  .sidebar { width: 100%; flex-direction: row; flex-wrap: wrap; justify-content: space-around; box-shadow: none; padding: 1em; }";
  message += "  .sidebar h1 { width: 100%; text-align: center; font-size: 1.2em; margin-bottom: 0.5em; }";
  message += "  .sidebar button { width: 100%; text-align: center; font-size: 1em; padding: 0.6em; }";
  message += "  .content { width: 100%; padding: 1em; }";
  message += "  .form-container { padding: 1em; }";
  message += "}";
  message += "</style></head><body>";

  message += "<div class='sidebar'>";
  message += "<h1>AIRWISE <i>Pro</i></h1>";
  message += "<button onclick=\"window.location.href='/info'\">IRIS Diagnostic</button>";
  message += "<button onclick=\"window.location.href='/pro'\">WiFi (Dynamic IP)</button>";
  message += "<button onclick=\"window.location.href='/pro1'\">WiFi (Static IP)</button>";
  message += "</div>";

  message += "<div class='content'>";

  message += "<div class='section'><h2>Device Identity</h2><table>";
  message += "<tr><th><span>Chip ID:</span></th><td>" + String(ESP.getChipId()) + "</td></tr>";
  message += "<tr><th><span>Device MAC:</span></th><td>" + WiFi.macAddress() + "</td></tr>";
  message += "<tr><th><span>Device AP Name:</span></th><td>";
  message += ssid;
  message += "</td></tr>";
  message += "</table></div>";

  message += "<div class='section'><h2>Sensors Readings</h2><table>";
  message += "<tr><th><span>Temperature (°C):</span></th><td>" + String(temp_str.c_str()) + "</td></tr>";
  message += "</table></div>";

  message += "<div class='section'><h2>WiFi Parameters</h2><table>";
  message += "<tr><th><span>Status:</span></th><td>" + String(WiFi.status() == WL_CONNECTED ? "Connected" : "Disconnected") + "</td></tr>";
  message += "<tr><th><span>SSID:</span></th><td>" + WiFi.SSID() + "</td></tr>";
  message += "<tr><th><span>RSSI:</span></th><td>" + String(WiFi.RSSI()) + " dBm</td></tr>";
  IPAddress localIP1 = WiFi.localIP();
  message += "<tr><th><span>IP Address:</span></th><td>" + localIP1.toString() + "</td></tr>";
  message += "<tr><th><span>Subnet:</span></th><td>" + a2 + "</td></tr>";
  message += "<tr><th><span>Gateway:</span></th><td>" + a3 + "</td></tr>";
  message += "<tr><th><span>Primary DNS:</span></th><td>" + a4 + "</td></tr>";
  message += "<tr><th><span>Secondary DNS:</span></th><td>" + a5 + "</td></tr>";
  message += "</table></div>";

  message += "<div class='section'><h2>Internet Services</h2><table>";
  message += "<tr><th><span>Server Connectivity:</span></th><td>" + String(server_connection_status ? "Connected" : "Disconnected") + "</td></tr>";
  message += "</table></div>";

  message += "<div class='section'><h2>IRIS Installation Information</h2><table>";
  message += "<tr><th><span>Building:</span></th><td>" + newBlock + "</td></tr>";
  message += "<tr><th><span>Room:</span></th><td>" + newRoom + "</td></tr>";
  message += "</table></div>";

  message += "</div></body></html>";

  server.send(200, "text/html", message);
}

void handleSetup() {
  if (server.hasArg("ssid") && server.hasArg("password") && server.hasArg("Block") && server.hasArg("Room")) {
    wifiCredentials = server.arg("ssid") + ":" + server.arg("password") + ":" + server.arg("Block") + ":" + server.arg("Room") + ":" + server.arg("connectionType");
    Serial.println("Received WiFi credentials:");
    Serial.println(wifiCredentials);

    newSsid = server.arg("ssid");
    newPassword = server.arg("password");
    newBlock = server.arg("Block");
    newRoom = server.arg("Room");
    WiFi.disconnect();
    store_dynamic_strings();
    mode = false;
    EEPROM.write(204, mode);

    String message;
    message.reserve(3000);

    message += "<!DOCTYPE html><html lang='en'><head>";
    message += "<meta charset='UTF-8'>";
    message += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    message += "<title>WiFi Connecting</title>";
    message += "<style>";
    message += "body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; display: flex; flex-direction: row; flex-wrap: wrap; min-height: 100vh; }";
    message += ".sidebar { width: 240px; background-color: #2c3e50; color: #ecf0f1; padding: 1.5em; display: flex; flex-direction: column; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }";
    message += ".sidebar h1 { font-size: 1.4em; margin-bottom: 1em; }";
    message += ".sidebar button { background-color: #34495e; border: none; color: white; padding: 0.8em; margin: 0.4em 0; text-align: left; border-radius: 6px; cursor: pointer; font-size: 1rem; }";
    message += ".sidebar button:hover { background-color: #1abc9c; }";
    message += ".content { flex: 1; padding: 2em; display: flex; align-items: center; justify-content: center; }";
    message += ".form-container { background: #fff; padding: 2em; width: 100%; max-width: 600px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }";
    message += ".form-container h2 { text-align: center; color: #2c3e50; }";
    message += "form { display: flex; flex-direction: column; }";
    message += "label { margin-top: 1em; font-weight: 600; color: #333; }";
    message += "input { padding: 0.6em; border: 1px solid #ccc; border-radius: 5px; font-size: 1rem; }";
    message += ".note { font-size: 0.8rem; color: #666; margin-top: 0.3em; }";
    message += "button.submit { margin-top: 1.5em; background-color: #007bff; color: white; border: none; padding: 0.8em; border-radius: 6px; font-size: 1rem; cursor: pointer; }";
    message += "button.submit:hover { background-color: #0056b3; }";
    message += "@media (max-width: 768px) { body { flex-direction: column; } .sidebar { width: 100%; flex-direction: row; flex-wrap: wrap; justify-content: space-around; box-shadow: none; } .sidebar h1 { width: 100%; text-align: center; font-size: 1.2em; margin-bottom: 0.5em; } .sidebar button { width: 100%; text-align: center; font-size: 1em; padding: 0.6em; } .content { padding: 1em; } .form-container { padding: 1em; } }";
    message += "</style></head><body>";

    message += "<div class='sidebar'>";
    message += "<h1>AIRWISE <i>Pro</i></h1>";
    message += "<button onclick=\"window.location.href='/info'\">Device Info</button>";
    message += "<button onclick=\"window.location.href='/pro'\">WiFi (Dynamic IP)</button>";
    message += "<button onclick=\"window.location.href='/pro1'\">WiFi (Static IP)</button>";
    message += "</div>";

    message += "<div class='content'>";
    message += "<h1>Connecting to WiFi...</h1>";
    message += "<p>Click the button below after 10 seconds to check device info.</p>";
    message += "<button class='redirect' onclick=\"window.location.href='/info'\">Device Info</button>";
    message += "</div></body></html>";

    server.send(200, "text/html", message);
    wifi_connect_dynamic();

    if (WiFi.status() == WL_CONNECTED) {
      Serial.println("Connected to WiFi network!");
    } else {
      server.send(200, "text/html", "<h1>Connection failed!</h1>");
      Serial.println("Failed to connect to WiFi network!");
    }
  } else {
    server.send(400, "text/plain", "Missing required fields");
  }
  Serial.print("Heap before HTML response: ");
  Serial.println(ESP.getFreeHeap());
}
void handleSetup1() {
  if (server.hasArg("ssid") && server.hasArg("password") && server.hasArg("ip") && server.hasArg("subnet") && server.hasArg("gateway") && server.hasArg("Block") && server.hasArg("Room") || (server.hasArg("dns1") || server.hasArg("dns2"))) {
    wifiCredentials = server.arg("ssid") + ":" + server.arg("password") + ":" + server.arg("ip") + ":" + server.arg("subnet") + ":" + server.arg("gateway") + ":" + server.arg("dns1") + ":" + server.arg("dns2");
    Serial.println("Received WiFi credentials:");
    Serial.println(wifiCredentials);

    newSsid = server.arg("ssid");
    newPassword = server.arg("password");
    a1 = server.arg("ip");
    a2 = server.arg("subnet");
    a3 = server.arg("gateway");
    a4 = server.arg("dns1");
    a5 = server.arg("dns2");
    newBlock = server.arg("Block");
    newRoom = server.arg("Room");
    WiFi.disconnect();
    store_static_strings();
    mode = true;
    EEPROM.write(204, mode);

    String message;
    message.reserve(3000);

    message += "<!DOCTYPE html><html lang='en'><head>";
    message += "<meta charset='UTF-8'>";
    message += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    message += "<title>WiFi Configuration</title>";
    message += "<style>";
    message += "*, ::before, ::after { box-sizing: border-box; }";
    message += "body { margin: 0; padding: 2em; font-family: 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f4f6f9; color: #2c3e50; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }";
    message += "h1, h2, h3 { text-align: center; margin-bottom: 0.5em; }";
    message += "p { font-size: 0.95rem; margin-bottom: 1em; text-align: center; color: #444; }";
    message += "button { border: none; color: white; background-color: #007bff; padding: 0.8rem 1.2rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: background-color 0.3s ease; width: 60%; max-width: 300px; }";
    message += "button:hover { background-color: #0056b3; }";
    message += "form { background-color: #fff; padding: 1.5em; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); width: 100%; max-width: 600px; margin-top: 2em; }";
    message += "input { padding: 0.6em; margin-top: 0.5em; border: 1px solid #ccc; border-radius: 5px; width: 100%; font-size: 1rem; }";
    message += "@media (max-width: 768px) { body { padding: 1em; } button { width: 100%; } form { width: 100%; padding: 1em; } }";
    message += "</style></head><body>";

    message += "<h1>AIRWISE <i>Pro</i></h1>";
    message += "<p>Click this button after 10 seconds.</p>";
    message += "<h3>Connecting to WiFi...</h3>";
    message += "<button type='button' onclick=\"window.location.href='/info'\">Device Info</button>";

    message += "</body></html>";
    server.send(200, "text/html", message);
    wifi_connect_static();

    if (WiFi.status() == WL_CONNECTED) {
      Serial.println("Connected to WiFi network!");
    } else {
      server.send(200, "text/html", "<h1>Connection failed!</h1>");
      Serial.println("Failed to connect to WiFi network!");
    }
  } else {
    server.send(400, "text/plain", "Missing required fields");
  }
  Serial.print("Heap before HTML response: ");
  Serial.println(ESP.getFreeHeap());
}
void wifi_connect_dynamic() {
  WiFi.begin(newSsid.c_str(), newPassword.c_str());
  int connectionTries = 0;
  while (WiFi.status() != WL_CONNECTED) {
    wifi_status_connection = false;
    Serial.print(".");
    ESP.getFreeHeap();
    WiFiClient client = server.client();
    server.handleClient();
  }
  if (WiFi.status() == WL_CONNECTED) {
    wifi_status_connection = true;
    digitalWrite(0, LOW);
  } else {
    wifi_status_connection = false;
    digitalWrite(0, HIGH);
  }
}
void wifi_connect_static() {
  ipStringToUint8Array(a1.c_str());
  subnetStringToUint8Array(a2.c_str());
  gatewayStringToUint8Array(a3.c_str());
  dns1StringToUint8Array(a4.c_str());
  dns2StringToUint8Array(a5.c_str());

  IPAddress local_IP = IPAddress(ipArray[0], ipArray[1], ipArray[2], ipArray[3]);
  Serial.println(local_IP);
  IPAddress gateway = IPAddress(gatewayArray[0], gatewayArray[1], gatewayArray[2], gatewayArray[3]);
  Serial.println(gateway);
  IPAddress subnet = IPAddress(subnetArray[0], subnetArray[1], subnetArray[2], subnetArray[3]);
  Serial.println(subnet);
  IPAddress dns1 = IPAddress(dns1Array[0], dns1Array[1], dns1Array[2], dns1Array[3]);
  Serial.println(dns1);
  IPAddress dns2 = IPAddress(dns2Array[0], dns2Array[1], dns2Array[2], dns2Array[3]);
  Serial.println(dns2);
  WiFi.config(local_IP, gateway, subnet, dns1, dns2);
  if (!WiFi.config(local_IP, gateway, subnet)) {
    Serial.println("STA Failed to configure");
  }
  WiFi.begin(newSsid.c_str(), newPassword.c_str());
  int connectionTries = 0;
  if (WiFi.status() != WL_CONNECTED) {
    wifi_status_connection = false;
    digitalWrite(0, HIGH);
    Serial.print(".");
    ESP.getFreeHeap();
    WiFiClient client = server.client();
    server.handleClient();
  }
  if (WiFi.status() == WL_CONNECTED) {
    wifi_status_connection = true;
    digitalWrite(0, LOW);
  } else {
    wifi_status_connection = false;
    digitalWrite(0, HIGH);
  }
}

// IP conversion helpers — unchanged from original
void ipStringToUint8Array(String ipString) {
  int index = 0;
  int dotIndex = ipString.indexOf('.');
  while (dotIndex != -1) {
    String octet1 = ipString.substring(0, dotIndex);
    ipArray[index] = octet1.toInt();
    index++;
    ipString = ipString.substring(dotIndex + 1);
    dotIndex = ipString.indexOf('.');
  }
  ipArray[index] = ipString.toInt();
  Serial.print("iparray:");
  Serial.println(ipArray[index]);
}

void subnetStringToUint8Array(String subnetString) {
  int index = 0;
  int dotIndex = subnetString.indexOf('.');
  while (dotIndex != -1) {
    String octet1 = subnetString.substring(0, dotIndex);
    subnetArray[index] = octet1.toInt();
    index++;
    subnetString = subnetString.substring(dotIndex + 1);
    dotIndex = subnetString.indexOf('.');
  }
  subnetArray[index] = subnetString.toInt();
}

void gatewayStringToUint8Array(String gatewayString) {
  int index = 0;
  int dotIndex = gatewayString.indexOf('.');
  while (dotIndex != -1) {
    String octet1 = gatewayString.substring(0, dotIndex);
    gatewayArray[index] = octet1.toInt();
    index++;
    gatewayString = gatewayString.substring(dotIndex + 1);
    dotIndex = gatewayString.indexOf('.');
  }
  gatewayArray[index] = gatewayString.toInt();
}

void dns1StringToUint8Array(String dns1String) {
  int index = 0;
  int dotIndex = dns1String.indexOf('.');
  while (dotIndex != -1) {
    String octet1 = dns1String.substring(0, dotIndex);
    dns1Array[index] = octet1.toInt();
    index++;
    dns1String = dns1String.substring(dotIndex + 1);
    dotIndex = dns1String.indexOf('.');
  }
  dns1Array[index] = dns1String.toInt();
}

void dns2StringToUint8Array(String dns2String) {
  int index = 0;
  int dotIndex = dns2String.indexOf('.');
  while (dotIndex != -1) {
    String octet1 = dns2String.substring(0, dotIndex);
    dns2Array[index] = octet1.toInt();
    index++;
    dns2String = dns2String.substring(dotIndex + 1);
    dotIndex = dns2String.indexOf('.');
  }
  dns2Array[index] = dns2String.toInt();
}

void tone_gen(int n) {
  for (int x = 0; x < n; x++) {
    digitalWrite(15, HIGH);
    delay(300);
    digitalWrite(15, LOW);
    delay(100);
  }
}

// ------------------------------------------------------------
// Backend communication — serial-number-only auth
// ------------------------------------------------------------

void addSerialHeader(HTTPClient& http) {
  http.addHeader("X-Device-Serial", deviceSerialNumber);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
}

String getTimestamp() {
  time_t now = time(nullptr);
  if (now == 0) {
    return "2026-01-01T00:00:00+00:00";
  }
  struct tm* timeinfo = gmtime(&now);
  char buf[32];
  snprintf(buf, sizeof(buf), "%04d-%02d-%02dT%02d:%02d:%02dZ",
           timeinfo->tm_year + 1900, timeinfo->tm_mon + 1, timeinfo->tm_mday,
           timeinfo->tm_hour, timeinfo->tm_min, timeinfo->tm_sec);
  return String(buf);
}

String makeEventId(const char* type) {
  return String(type) + "-" + String(deviceSerialNumber) + "-" + String(millis());
}

// 1. Heartbeat — tell backend the device is online
void sendHeartbeat() {
  WiFiClientSecure client;
  client.setInsecure();

  String endpoint = String(baseUrl) + "/device/heartbeat";
  if (https.begin(client, endpoint)) {
    addSerialHeader(https);

    String eventId = makeEventId("hb");
    String payload = "{";
    payload += "\"schema_version\": 1,";
    payload += "\"event_id\": \"" + eventId + "\",";
    payload += "\"occurred_at\": \"" + getTimestamp() + "\",";
    payload += "\"firmware_version\": \"1.2.0\",";
    payload += "\"wifi_rssi_dbm\": " + String(WiFi.RSSI()) + ",";
    payload += "\"uptime_seconds\": " + String(millis() / 1000);
    payload += "}";

    Serial.println("Sending heartbeat...");
    int httpCode = https.POST(payload);

    if (httpCode > 0) {
      Serial.printf("Heartbeat HTTP %d\n", httpCode);
      String response = https.getString();
      Serial.println(response);
      server_connection_status = true;
    } else {
      Serial.printf("Heartbeat failed: %s\n", https.errorToString(httpCode).c_str());
      server_connection_status = false;
    }

    https.end();
  } else {
    Serial.println("Failed to connect for heartbeat.");
    server_connection_status = false;
  }
}

// Report current AC state to backend
void reportState() {
  WiFiClientSecure client;
  client.setInsecure();

  String endpoint = String(baseUrl) + "/device/state";
  if (https.begin(client, endpoint)) {
    addSerialHeader(https);

    String eventId = makeEventId("state");
    String payload = "{";
    payload += "\"schema_version\": 1,";
    payload += "\"event_id\": \"" + eventId + "\",";
    payload += "\"occurred_at\": \"" + getTimestamp() + "\",";
    payload += "\"state\": {";
    payload += "\"power\": \"" + String(ACStatus ? "on" : "off") + "\",";
    payload += "\"temperature_setpoint_c\": " + String(ACTemp);
    payload += "}";
    payload += "}";

    Serial.println("Reporting state: power=" + String(ACStatus ? "on" : "off") + ", temp=" + String(ACTemp));
    int httpCode = https.POST(payload);

    if (httpCode > 0) {
      Serial.printf("State report HTTP %d\n", httpCode);
      String response = https.getString();
      Serial.println(response);
    } else {
      Serial.printf("State report failed: %s\n", https.errorToString(httpCode).c_str());
    }

    https.end();
  } else {
    Serial.println("Failed to connect for state report.");
  }
}

// Poll for pending commands, execute each, and report results
void pollAndExecuteCommands() {
  WiFiClientSecure client;
  client.setInsecure();

  String endpoint = String(baseUrl) + "/device/commands/pending";
  if (https.begin(client, endpoint)) {
    addSerialHeader(https);

    int httpCode = https.GET();

    if (httpCode > 0) {
      String response = https.getString();
      Serial.println("Pending commands response:");
      Serial.println(response);

      DynamicJsonDocument doc(2048);
      DeserializationError error = deserializeJson(doc, response);

      if (!error) {
        JsonArray commands = doc["data"];
        if (commands.isNull()) {
          Serial.println("No pending commands.");
        } else {
          for (JsonObject cmd : commands) {
            const char* cmdId = cmd["id"] | "";
            const char* cmdType = cmd["command_type"] | "";
            JsonObject desiredState = cmd["desired_state"];

            Serial.print("Executing command: ");
            Serial.println(cmdId);

            // Acknowledge receipt
            acknowledgeCommand(cmdId, "accepted");

            // Execute the command (handles power on/off, temperature set, increase/decrease)
            executeCommand(desiredState);

            // Report transmission result
            reportTransmissionResult(cmdId, "transmitted");

            // Report updated state
            reportState();
          }
        }
      } else {
        Serial.print("JSON parse error: ");
        Serial.println(error.c_str());
      }
    } else {
      Serial.printf("Poll commands failed: %s\n", https.errorToString(httpCode).c_str());
    }

    https.end();
  } else {
    Serial.println("Failed to connect for command polling.");
  }
}

// Acknowledge a command receipt
void acknowledgeCommand(const char* cmdId, const char* result) {
  WiFiClientSecure client;
  client.setInsecure();

  String endpoint = String(baseUrl) + "/device/commands/" + String(cmdId) + "/ack";
  if (https.begin(client, endpoint)) {
    addSerialHeader(https);

    String eventId = makeEventId("ack");
    String payload = "{";
    payload += "\"schema_version\": 1,";
    payload += "\"event_id\": \"" + eventId + "\",";
    payload += "\"occurred_at\": \"" + getTimestamp() + "\",";
    payload += "\"result\": \"" + String(result) + "\"";
    payload += "}";

    int httpCode = https.POST(payload);
    if (httpCode > 0) {
      Serial.printf("ACK HTTP %d for %s\n", httpCode, cmdId);
    } else {
      Serial.printf("ACK failed: %s\n", https.errorToString(httpCode).c_str());
    }

    https.end();
  }
}

// Report IR transmission result
void reportTransmissionResult(const char* cmdId, const char* result) {
  WiFiClientSecure client;
  client.setInsecure();

  String endpoint = String(baseUrl) + "/device/commands/" + String(cmdId) + "/transmission-result";
  if (https.begin(client, endpoint)) {
    addSerialHeader(https);

    String eventId = makeEventId("tx");
    String payload = "{";
    payload += "\"schema_version\": 1,";
    payload += "\"event_id\": \"" + eventId + "\",";
    payload += "\"occurred_at\": \"" + getTimestamp() + "\",";
    payload += "\"result\": \"" + String(result) + "\"";
    payload += "}";

    int httpCode = https.POST(payload);
    if (httpCode > 0) {
      Serial.printf("TX-result HTTP %d for %s\n", httpCode, cmdId);
    } else {
      Serial.printf("TX-result failed: %s\n", https.errorToString(httpCode).c_str());
    }

    https.end();
  }
}

// Execute a command on the simulated AC (IR send + update local state)
// Supported commands from backend:
//   - power: "on"   -> turn AC ON
//   - power: "off"  -> turn AC OFF
//   - temperature_setpoint_c: N  -> set temperature to N (absolute)
//     (This also covers "increase"/"decrease": the backend sends the new
//      absolute target temperature, and the device applies it.)
void executeCommand(JsonObject desiredState) {
  if (desiredState.isNull()) {
    Serial.println("No desired_state in command.");
    return;
  }

  const char* power = desiredState["power"] | nullptr;
  int temp = desiredState["temperature_setpoint_c"] | -1;

  if (power != nullptr) {
    if (strcmp(power, "on") == 0) {
      ACStatus = true;
      ac.on();
      Serial.println("AC Power: ON");
    } else if (strcmp(power, "off") == 0) {
      ACStatus = false;
      ac.off();
      Serial.println("AC Power: OFF");
    }
  }

  if (temp != -1) {
    ACTemp = temp;
    ac.setTemp(temp);
    Serial.print("AC Setpoint: ");
    Serial.println(ACTemp);
  }

#if SEND_GREE
  Serial.println("Sending IR command to A/C...");
  ac.send();
#endif
}

// ------------------------------------------------------------
void printState() {
  Serial.println("GREE A/C remote is in the following state:");
  Serial.printf("  %s\n", ac.toString().c_str());
  unsigned char* ir_code = ac.getRaw();
  Serial.print("IR Code: 0x");
  for (uint8_t i = 0; i < kGreeStateLength; i++)
    Serial.printf("%02X", ir_code[i]);
  Serial.println();
}
