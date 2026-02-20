const express = require("express");
const fetch = global.fetch || require("node-fetch");
const { JSDOM } = require("jsdom");

const app = express();
app.use(express.json());

const WEBFLOW_TOKEN = process.env.WEBFLOW_TOKEN;
if (!WEBFLOW_TOKEN) {
  console.error("Missing WEBFLOW_TOKEN");
  process.exit(1);
}

async function fetchText(url) {
  const resp = await fetch(url);
  if (!resp.ok) throw new Error(`Fetch failed ${resp.status}`);
  const html = await resp.text();
  const dom = new JSDOM(html);
  return dom.window.document.body.textContent.replace(/\s+/g, " ").trim();
}

app.post("/rpc", async (req, res) => {
  const { method, params, id } = req.body;

  try {
    switch (method) {
      case "listSites": {
        const resp = await fetch("https://api.webflow.com/sites", {
          headers: {
            Authorization: `Bearer ${WEBFLOW_TOKEN}`,
            Accept: "application/json"
          }
        });
        if (!resp.ok) throw new Error(`Webflow error ${resp.status}`);
        const data = await resp.json();
        return res.json({ jsonrpc: "2.0", id, result: data });
      }

      case "getHelpPage": {
        const { url } = params || {};
        if (!url) throw new Error("Missing url param");
        const content = await fetchText(url);
        return res.json({ jsonrpc: "2.0", id, result: content });
      }

      case "status": {
        return res.json({ jsonrpc: "2.0", id, result: "OK" });
      }

      default:
        return res.json({
          jsonrpc: "2.0",
          id,
          error: { code: -32601, message: "Method not found" }
        });
    }
  } catch (err) {
    console.error("Error:", err.message);
    return res.json({
      jsonrpc: "2.0",
      id,
      error: { code: -32000, message: err.message }
    });
  }
});

app.listen(3000, () => console.log("Server running on port 3000"));
