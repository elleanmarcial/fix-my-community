(function () {
  const $ = (s, r) => (r || document).querySelector(s);
  const live = (m) => { const l = $('#live'); if (l) l.textContent = m; };

  /* Confirmation dialog for status changes */
  let dlg;
  function ask(title, text, yes, cb) {
    if (!dlg) {
      dlg = document.createElement('dialog');
      dlg.innerHTML = '<h3 id="fmc-dt"></h3><p id="fmc-dp"></p><div style="display:flex;gap:8px;justify-content:flex-end"><button type="button" class="btn" id="fmc-no">Cancel</button><button type="button" class="btn primary" id="fmc-yes"></button></div>';
      dlg.setAttribute('aria-labelledby', 'fmc-dt');
      document.body.appendChild(dlg);
      $('#fmc-no', dlg).onclick = () => dlg.close();
    }
    $('#fmc-dt', dlg).textContent = title;
    $('#fmc-dp', dlg).textContent = text;
    const y = $('#fmc-yes', dlg);
    y.textContent = yes;
    y.onclick = () => { dlg.close(); cb(); };
    dlg.showModal();
  }
  document.addEventListener('submit', (e) => {
    const f = e.target;
    if (!f.matches || !f.matches('[data-confirm-status]')) return;
    const sel = $('select[name=status]', f);
    if (!sel || sel.value === sel.dataset.initial) return;
    e.preventDefault();
    ask('Change status?', 'This report will move from ' + sel.dataset.initial + ' to ' + sel.value + '. The resident will see the new status.', 'Save changes', () => f.submit());
  });

  /* Client-side filtering of the report queue */
  document.querySelectorAll('[data-filter-table]').forEach((box) => {
    const q = $('[data-filter-q]', box), st = $('[data-filter-status]', box), ct = $('[data-filter-category]', box);
    const rows = [...box.querySelectorAll('tr[data-row]')], empty = $('[data-empty]', box);
    const run = () => {
      let n = 0;
      rows.forEach((r) => {
        const ok = (!q.value || r.dataset.text.includes(q.value.toLowerCase())) && (!st.value || r.dataset.status === st.value) && (!ct.value || r.dataset.category === ct.value);
        r.hidden = !ok; if (ok) n++;
      });
      if (empty) empty.hidden = n > 0;
      live(n + (n === 1 ? ' report shown' : ' reports shown'));
    };
    [q, st, ct].forEach((i) => i && i.addEventListener('input', run));
    const c = $('[data-clear]', box);
    if (c) c.addEventListener('click', () => { q.value = ''; st.value = ''; ct.value = ''; run(); });
  });

  /* Dashboard charts (SVG, themed through CSS variables) */
  const src = $('#fmc-chart-data');
  if (!src) return;
  const D = JSON.parse(src.textContent);
  const SC = { 'Submitted': 'var(--blue)', 'Verified': 'var(--amber)', 'In Progress': 'var(--green)', 'Resolved': 'var(--slate)', 'Rejected': 'var(--red)' };
  const ORDER = ['Resolved', 'In Progress', 'Verified', 'Submitted', 'Rejected'];
  const WK = D.weeks;
  const nice = (m) => { const S = [1, 2, 5, 10, 20, 50, 100, 200, 500, 1000]; const s = S.find((x) => m / x <= 5) || 1000; return [Math.max(s, Math.ceil(m / s) * s), s]; };
  const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

  function chart(label, maxVal, body) {
    const [max, step] = nice(maxVal);
    const CW = 560, CH = 230, L = 34, T = 10, B = 26, R = 8;
    const y = (v) => T + (CH - T - B) * (1 - v / max), bw = (CW - L - R) / WK.length;
    let g = '';
    for (let v = 0; v <= max; v += step) g += '<line x1="' + L + '" x2="' + (CW - R) + '" y1="' + y(v) + '" y2="' + y(v) + '" style="stroke:var(--line)"/><text x="' + (L - 6) + '" y="' + (y(v) + 4) + '" text-anchor="end">' + v + '</text>';
    const xs = WK.map((w, i) => '<text x="' + (L + bw * (i + .5)) + '" y="' + (CH - 8) + '" text-anchor="middle">' + esc(w) + '</text>').join('');
    return '<svg viewBox="0 0 ' + CW + ' ' + CH + '" role="img" aria-label="' + esc(label) + '">' + g + xs + body(y, bw, L) + '</svg>';
  }
  const total = (i) => ORDER.reduce((a, k) => a + (D.stack[k] ? D.stack[k][i] : 0), 0);

  const keys = ['Submitted', 'Verified', 'In Progress', 'Resolved', 'Rejected'].filter((k) => D.stack[k] && D.stack[k].some((v) => v > 0));
  $('#c-stack').innerHTML = chart('Reports received each week, by current status', Math.max(1, ...WK.map((w, i) => total(i))), (y, bw, L) =>
    WK.map((w, i) => { let acc = 0; return ORDER.map((k) => { const v = D.stack[k] ? D.stack[k][i] : 0, y1 = y(acc + v), h = y(acc) - y1; acc += v; return v ? '<rect x="' + (L + bw * (i + .2)) + '" y="' + y1 + '" width="' + (bw * .6) + '" height="' + h + '" style="fill:' + SC[k] + '"><title>' + esc(w) + ': ' + k + ' ' + v + '</title></rect>' : ''; }).join(''); }).join('')
  ) + '<ul class="lg">' + (keys.length ? keys : ['Submitted']).map((k) => '<li><i style="background:' + SC[k] + '"></i>' + k + '</li>').join('') + '</ul>';

  $('#c-bar').innerHTML = chart('Reports resolved each week', Math.max(1, ...D.resolved), (y, bw, L) =>
    D.resolved.map((v, i) => '<rect x="' + (L + bw * (i + .2)) + '" y="' + y(v) + '" width="' + (bw * .6) + '" height="' + (y(0) - y(v)) + '" style="fill:var(--forest)"/><text x="' + (L + bw * (i + .5)) + '" y="' + (y(v) - 4) + '" text-anchor="middle">' + v + '</text>').join(''));

  const days = D.days;
  $('#c-line').innerHTML = days.some((v) => v !== null)
    ? chart('Average days to resolve, by week of resolution', Math.max(1, ...days.map((v) => v || 0)), (y, bw, L) => {
        const p = days.map((v, i) => v === null ? null : [L + bw * (i + .5), y(v), v, i]).filter(Boolean);
        return '<polyline points="' + p.map((a) => a[0] + ',' + a[1]).join(' ') + '" fill="none" stroke-width="2.5" style="stroke:var(--forest)"/>' + p.map((a) => '<circle cx="' + a[0] + '" cy="' + a[1] + '" r="4" style="fill:var(--forest)"><title>' + esc(WK[a[3]]) + ': ' + a[2] + ' days</title></circle>').join('');
      })
    : '<p>No reports have been resolved in this period yet.</p>';

  const cats = D.categories, tot = cats.reduce((a, c) => a + c[1], 0);
  const PAL = ['var(--forest)', 'var(--blue)', 'var(--amber)', 'var(--slate)', '#5C9A7F', '#A9CDB9'];
  if (tot === 0) { $('#c-donut').innerHTML = '<p>No reports yet. Categories will appear here once residents start reporting.</p>'; }
  else {
    const r = 62, C = 2 * Math.PI * r; let off = 0;
    const arcs = cats.map((c, i) => { const len = c[1] / tot * C; const s = '<circle cx="90" cy="90" r="' + r + '" fill="none" stroke-width="26" style="stroke:' + PAL[i % PAL.length] + '" stroke-dasharray="' + len + ' ' + (C - len) + '" stroke-dashoffset="' + (-off) + '" transform="rotate(-90 90 90)"><title>' + esc(c[0]) + ': ' + c[1] + '</title></circle>'; off += len; return s; }).join('');
    $('#c-donut').innerHTML = '<svg viewBox="0 0 180 180" role="img" aria-label="Reports by category"><g>' + arcs + '</g><text x="90" y="92" text-anchor="middle" style="font:800 24px var(--head);fill:var(--ink)">' + tot + '</text><text x="90" y="110" text-anchor="middle">reports</text></svg><ul class="lg col">' + cats.map((c, i) => '<li><i style="background:' + PAL[i % PAL.length] + '"></i>' + esc(c[0]) + ': ' + c[1] + ' (' + Math.round(c[1] / tot * 100) + '%)</li>').join('') + '</ul>';
  }
})();
