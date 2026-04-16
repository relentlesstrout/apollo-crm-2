import React from 'react'
import { createRoot } from 'react-dom/client'
import Table from './components/Table.jsx'

const registry = {
    Table: Table,
}

document.querySelectorAll('[data-react-component]').forEach((el) => {
    const name = el.dataset.reactComponent
    const Component = registry[name]

    if (!Component) return

    const props = el.dataset.reactProps
        ? JSON.parse(el.dataset.reactProps)
        : {}

    createRoot(el).render(<Component {...props} />)
})
